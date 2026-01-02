FROM python:3.10-slim

WORKDIR /app

# Установка зависимостей
RUN pip install --no-cache-dir flask sentence-transformers torch --extra-index-url https://download.pytorch.org/whl/cpu

COPY app.py .

# Предзагрузка модели при сборке (опционально)
RUN python -c "from sentence_transformers import SentenceTransformer; SentenceTransformer('paraphrase-multilingual-MiniLM-L12-v2')"

EXPOSE 5000

CMD ["python", "app.py"]
