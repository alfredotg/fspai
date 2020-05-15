FROM php:cli
EXPOSE 8000
WORKDIR /app
COPY ./ ./
RUN cp ./.env.example ./.env && \
    php artisan key:generate && \
    touch ./database/database.sqlite && \
    php artisan migrate && \
    php artisan db:seed --force && \
    php artisan storage:link
CMD ["php", "artisan", "serve", "--host", "0"]

