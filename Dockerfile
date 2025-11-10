FROM node:13-alpine AS frontend_builder
WORKDIR /usr/src/app
COPY frontend/JWT/package*.json ./
RUN npm install
COPY frontend/JWT/ ./
RUN npm run build

FROM golang:1.13-alpine AS backend_builder
RUN apk --update add --no-cache git
RUN export GOBIN=$HOME/work/bin
WORKDIR /go/src/app
ADD backend/JWT/. .
RUN go get -d -v ./...
RUN CGO_ENABLED=0 go build -o main .

FROM alpine:3.11
RUN adduser -S -D -H -h /app appuser
USER appuser
COPY --from=frontend_builder /usr/src/app/build/. /app/static/
COPY --from=backend_builder /go/src/app/main /app/
WORKDIR /app
CMD ["./main"]