from flask import Flask, request, jsonify
from sentence_transformers import SentenceTransformer
import logging

app = Flask(__name__)
logging.basicConfig(level=logging.INFO)

# Загрузка модели при старте
model = SentenceTransformer('paraphrase-multilingual-MiniLM-L12-v2')
logging.info("Model loaded successfully")

@app.route('/embed', methods=['POST'])
def embed():
    try:
        data = request.json
        
        # Поддержка одного текста или массива
        if isinstance(data, dict):
            if 'text' in data:
                texts = [data['text']]
            elif 'texts' in data:
                texts = data['texts']
            else:
                return jsonify({'error': 'Missing text or texts field'}), 400
        else:
            return jsonify({'error': 'Invalid input format'}), 400
        
        # Генерация embeddings
        embeddings = model.encode(texts, convert_to_numpy=True)
        
        # Возврат результата
        if len(texts) == 1:
            return jsonify({'embedding': embeddings[0].tolist()})
        else:
            return jsonify({'embeddings': embeddings.tolist()})
            
    except Exception as e:
        logging.error(f"Error: {str(e)}")
        return jsonify({'error': str(e)}), 500

@app.route('/health', methods=['GET'])
def health():
    return jsonify({'status': 'ok', 'model': 'paraphrase-multilingual-MiniLM-L12-v2'})

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
