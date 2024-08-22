FROM php:8.3-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid
ARG password

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN apt-get update && \
    apt-get install -y libpq-dev && \
    docker-php-ext-install pdo pdo_pgsql pgsql
# Install PHP extensions
#RUN docker-php-ext-install mbstring exif pcntl bcmath gd
RUN pecl update-channels && \
	pecl install redis

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./.docker-conf/php /usr/local/etc/php

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN apt-get -y update && \
	apt-get -y install git

#install RSA key for git
RUN mkdir /root/.ssh/
ADD ./.docker-conf/.ssh/id_rsa /root/.ssh/id_rsa
#ADD ./.docker-conf/.ssh/config /root/.ssh/config
ADD ./.docker-conf/.ssh/id_rsa.pub /root/.ssh/id_rsa.pub
RUN chmod 600 /root/.ssh/id_rsa
RUN chmod 600 /root/.ssh/id_rsa.pub
RUN eval `ssh-agent -s` && \
    ssh-add /root/.ssh/id_rsa

# Create known_hosts
RUN touch /root/.ssh/known_host
# Add gitlab key
RUN ssh-keyscan gitlab.com >> /root/.ssh/known_hosts

#for user `git` add key:
RUN mkdir /home/git/.ssh/
ADD ./.docker-conf/.ssh/id_rsa /home/git/.ssh/id_rsa
ADD ./.docker-conf/.ssh/config /home/git/.ssh/config
ADD ./.docker-conf/.ssh/id_rsa.pub /home/git/.ssh/id_rsa.pub
RUN chmod 600 /home/git/.ssh/id_rsa
RUN chmod 600 /home/git/.ssh/id_rsa.pub
RUN eval `ssh-agent -s` && \
    ssh-add /home/git/.ssh/id_rsa

# Create known_hosts
RUN touch /home/git/.ssh/known_host
# Add gitlab key
RUN ssh-keyscan github.com >> /home/git/.ssh/known_hosts

# Clone the conf files into the docker container
RUN git clone https://github.com/sneef/ftt_task.git ./ && \
    chmod g+w .git -R

RUN composer install

COPY ./.env.example ./.env
ADD ./.env /var/www/html/.env

RUN	chmod -R a+rw /var/www/html/storage && \
    chmod -R 777 /var/www/html/bootstrap/cache && \
    usermod -aG root $user

RUN git config --global --add safe.directory /var/www/html

RUN usermod -aG sudo git

RUN apt-get -y install npm && \
    npm install && \
    npm run build
#RUN php artisan migrate

USER root
