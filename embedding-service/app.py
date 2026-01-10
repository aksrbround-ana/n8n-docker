from flask import Flask, request, jsonify
from sentence_transformers import SentenceTransformer
import logging

app = Flask(__name__)
logging.basicConfig(level=logging.INFO)

# Используем модель с размерностью 768
model = SentenceTransformer('paraphrase-multilingual-mpnet-base-v2')
logging.info("Model loaded successfully")

@app.route('/embed', methods=['POST'])
def embed():
    try:
        data = request.json
        logging.info(f"Received request with {len(data) if isinstance(data, list) else 1} items")
        
        # Поддержка разных форматов
        if isinstance(data, list):
            texts = [item.get('text', '') for item in data]
        elif isinstance(data, dict):
            if 'text' in data:
                texts = [data['text']]
            elif 'texts' in data:
                texts = data['texts']
            else:
                return jsonify({'error': 'Missing text or texts field'}), 400
        else:
            return jsonify({'error': 'Invalid input format'}), 400
        
        texts = [t for t in texts if t]
        
        if not texts:
            return jsonify({'error': 'No valid texts provided'}), 400
        
        embeddings = model.encode(texts, convert_to_numpy=True)
        
        logging.info(f"Generated {len(embeddings)} embeddings, dimension: {embeddings.shape[1]}")
        
        if len(texts) == 1:
            return jsonify({'embedding': embeddings[0].tolist()})
        else:
            return jsonify({'embeddings': embeddings.tolist()})
            
    except Exception as e:
        logging.error(f"Error: {str(e)}")
        return jsonify({'error': str(e)}), 500

@app.route('/health', methods=['GET'])
def health():
    return jsonify({
        'status': 'ok', 
        'model': 'paraphrase-multilingual-mpnet-base-v2',
        'dimension': 768
    })