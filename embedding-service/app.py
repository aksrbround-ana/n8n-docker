from flask import Flask, request, jsonify
import numpy as np
from sentence_transformers import SentenceTransformer
import psycopg2
import os

app = Flask(__name__)
model = SentenceTransformer('paraphrase-multilingual-MiniLM-L12-v2')

# Конфигурация БД из переменных окружения
DB_CONFIG = {
    'host': os.getenv('POSTGRES_HOST', 'postgres'),
    'database': os.getenv('POSTGRES_DB', 'hub_data'),
    'user': os.getenv('POSTGRES_USER', 'postgres'),
    'password': os.getenv('POSTGRES_PASSWORD', 'password'),
    'port': os.getenv('POSTGRES_PORT', 5432)
}

# Существующий endpoint для embeddings
@app.route('/embed', methods=['POST'])
def embed():
    texts = request.json['texts']
    
    # Генерируем embeddings
    embeddings = model.encode(texts)
    
    # Конвертируем в список для JSON
    if len(texts) == 1:
        return jsonify({'embedding': embeddings.tolist()})
    else:
        return jsonify({'embeddings': embeddings.tolist()})


# НОВЫЙ endpoint для проверки дубликатов
@app.route('/check_duplicates', methods=['POST'])
def check_duplicates():
    """
    Проверяет, являются ли новые вопросы дубликатами существующих в БД
    
    Input: {"questions": ["вопрос 1", "вопрос 2", ...]}
    Output: {"is_duplicate": [false, true, false, ...]}
    """
    questions = request.json['questions']
    
    # Получаем embeddings новых вопросов
    new_embeddings = model.encode(questions)
    
    # Подключаемся к БД и получаем существующие embeddings
    try:
        conn = psycopg2.connect(**DB_CONFIG)
        cur = conn.cursor()
        
        # Получаем все вопросы и их embeddings из БД
        cur.execute("""
            SELECT question_ru, embedding 
            FROM faq_entries 
            WHERE embedding IS NOT NULL
        """)
        
        existing = cur.fetchall()
        cur.close()
        conn.close()
        
        # Если БД пустая, все вопросы уникальные
        if not existing:
            return jsonify({
                'is_duplicate': [False] * len(questions)
            })
        
        # Конвертируем embeddings в numpy массив
        existing_embeddings = np.array([e[1] for e in existing])
        
        # Проверяем схожесть каждого нового вопроса с существующими
        results = []
        for new_emb in new_embeddings:
            # Вычисляем cosine similarity со всеми существующими
            similarities = np.dot(existing_embeddings, new_emb) / (
                np.linalg.norm(existing_embeddings, axis=1) * np.linalg.norm(new_emb)
            )
            
            max_similarity = np.max(similarities)
            
            # Порог схожести 0.85 (можно настроить)
            # Если схожесть > 0.85, считаем дубликатом
            is_duplicate = max_similarity > 0.85
            
            results.append(is_duplicate)
        
        return jsonify({
            'is_duplicate': results
        })
        
    except Exception as e:
        return jsonify({
            'error': str(e),
            'is_duplicate': [False] * len(questions)  # В случае ошибки пропускаем все
        }), 500


@app.route('/health', methods=['GET'])
def health():
    """Проверка здоровья сервиса"""
    return jsonify({'status': 'ok'})


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=False)