ARG ASSETS_IMAGE
ARG ASSETS_IMAGE
FROM ${ASSETS_IMAGE} as assets

FROM nginx:1.17-alpine

COPY ./docker/nginx/nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=assets /var/www/app.loc/public/ /var/www/app.loc/public/

ARG NGINX_PORT
RUN sed -i "s|__LISTEN_PORT__|$NGINX_PORT|" /etc/nginx/conf.d/default.conf
