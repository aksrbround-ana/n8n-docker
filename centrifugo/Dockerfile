FROM centrifugo/centrifugo:latest
WORKDIR /centrifugo
COPY config.json ./config.json
EXPOSE 8000
CMD ["centrifugo", "--config", "config.json"]
