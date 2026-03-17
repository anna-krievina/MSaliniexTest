# start from existing PHP image with the version matching project PHP
FROM php:8.4-cli

# set the working directory (name doesn't matter)
WORKDIR /api

# install tools composer needs
RUN apt-get update && apt-get install -y \
    git \
    unzip

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# copy the project (check .dockerignore for ignored files)
COPY . .

# skip user interaction, prefer zip file over full git repository, skip require-dev, autoloader increases performance
RUN composer install --no-interaction --prefer-dist --no-dev --optimize-autoloader

# create the var directory for Symfony. It should be optional but this way there shouldn't be problems
RUN mkdir -p var/cache var/log

# the port the container will use (outside port is specified in the run command)
EXPOSE 8000

# for running the server (project was run with php -S localhost:8000 -t public)
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
