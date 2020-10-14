FROM tunet/php:7.4.6-fpm-v3

RUN apk update \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
    && apk --no-cache add \
        git \
        zsh \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        zip \
        libzip-dev \
    && pecl install xdebug-2.9.5 \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install -j2 gd \
    && docker-php-ext-install zip \
    && apk del .build-deps

RUN addgroup --gid 1000 docker \
    && adduser --uid 1000 --ingroup docker --home /home/docker --shell /bin/zsh --disabled-password --gecos "" docker

USER 1000

RUN wget https://github.com/robbyrussell/oh-my-zsh/raw/master/tools/install.sh -O - | zsh || true
RUN echo 'export ZSH=/home/docker/.oh-my-zsh' > ~/.zshrc \
    && echo 'ZSH_THEME="simple"' >> ~/.zshrc \
    && echo 'plugins=(npm)' >> ~/.zshrc \
    && echo 'source $ZSH/oh-my-zsh.sh' >> ~/.zshrc \
    && echo 'PROMPT="%{$fg_bold[yellow]%}php_cli@docker_monitor %{$fg_bold[blue]%}%(!.%1~.%~)%{$reset_color%} "' > ~/.oh-my-zsh/themes/simple.zsh-theme

WORKDIR /var/www/app.loc
