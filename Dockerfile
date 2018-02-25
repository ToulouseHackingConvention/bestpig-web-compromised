FROM debian:stretch

ARG WD=/srv/www
ARG SRC="${WD}/compromised/"

RUN export DEBIAN_FRONTEND=noninteractive && \
    apt-get update && \
		apt-get -y upgrade && \
		apt-get -y install zip nginx php-fpm && \
		service php7.0-fpm start && \
	  service nginx stop && \
    rm /etc/nginx/sites-enabled/default

# nginx
ADD ./nginx.conf /etc/nginx/sites-enabled/default

RUN sed -i "s#_ROOT_#${SRC}#g" /etc/nginx/sites-enabled/default

# php files
ADD ./src "${SRC}"

EXPOSE 80

RUN service php7.0-fpm restart

RUN echo "daemon off;" >> /etc/nginx/nginx.conf

RUN (cd $SRC; zip -r sources.zip admin.php assets/ base/ *.php)

CMD service php7.0-fpm restart && nginx
