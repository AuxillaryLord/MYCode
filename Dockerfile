FROM php:8.0-cli

# Install MySQL extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Set working directory
WORKDIR /app

# Start PHP built-in server
CMD ["php", "-S", "0.0.0.0:8000", "-t", "."]
