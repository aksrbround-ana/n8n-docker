FROM python:3.10-slim

WORKDIR /app

# Установка зависимостей
RUN pip install --no-cache-dir \
    flask \
    gunicorn \
    sentence-transformers \
    torch --extra-index-url https://download.pytorch.org/whl/cpu

COPY app.py .

# Предзагрузка модели при сборке (опционально)
RUN python -c "from sentence_transformers import SentenceTransformer; SentenceTransformer('paraphrase-multilingual-MiniLM-L12-v2')"

EXPOSE 5000

# Используем Gunicorn вместо Flask dev server
CMD ["gunicorn", "--bind", "0.0.0.0:5000", "--workers", "2", "--timeout", "120", "app:app"]