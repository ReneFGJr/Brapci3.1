FROM php:8.0.20-apache

#====================================================================#
#                         SET VERSION LABEL                          #
#====================================================================#
ARG BUILD_DATE="June 17 2022"
ARG PHP_VERSION="8.0"

ENV BUILD_DATE="${BUILD_DATE}"
ENV PHP_VERSION="${PHP_VERSION}"

#====================================================================#
#                             SET LABELS                             #
#====================================================================#
LABEL build_version="PHP: ${PHP_VERSION}"
LABEL build_date="${BUILD_DATE}"
LABEL maintainer="Antonio Sanna <atsanna@tiscali.it>"

#====================================================================#
#                         SET SERVER NAME                            #
#====================================================================#
ARG SERVERNAME="localhost"
ARG DOMAIN="example.com"
ARG WWWDOMAIN="www.example.com"
ARG TZ="Europe/Rome"

ENV SERVERNAME="${SERVERNAME}"
ENV DOMAIN="${DOMAIN}"
ENV WWWDOMAIN="${WWWDOMAIN}"

#====================================================================#
#                          SET USER NAME                             #
#====================================================================#
ARG USER="gisadmin"

#====================================================================#
#                          UPGRADE SYSTEM                            #
#====================================================================#
RUN \
    DEBIAN_FRONTEND=noninteractive \
    apt-get update && \
    apt-get -y upgrade

#====================================================================#
#                          INSTALL UTILITY                           #
#====================================================================#
RUN apt-get -y install --fix-missing sudo \
    gpg \
    vim \
    wget \
    git \
    software-properties-common

#====================================================================#
#                          ADD REPOSITORY                            #
#====================================================================#
RUN sed -i -e "s|# export LS_OPTIONS=|export LS_OPTIONS=|g" -e "s|# alias ls=|alias ls=|g" -e "s|# alias ll=|alias ll=|g" -e "s|# alias rm=|alias rm=|g" ~/.bashrc \
    && ln -snf /usr/share/zoneinfo/$TZ /etc/localtime &&  echo $TZ > /etc/timezone \
    && echo "deb http://deb.debian.org/debian buster-backports main contrib non-free" > /etc/apt/sources.list.d/backports.list

#====================================================================#
#                          UPGRADE SYSTEM                            #
#====================================================================#
RUN apt-get update && \
    apt-get -y upgrade

#====================================================================#
#                          INSTALL CURL                              #
#====================================================================#
RUN apt-get -y install --fix-missing curl

#====================================================================#
#                           INSTALL GIT                              #
#====================================================================#
RUN apt-get -y install --fix-missing git

#====================================================================#
#                       INSTALL ZIP - UNZIP                          #
#====================================================================#
RUN apt-get -y install --fix-missing zip unzip

#====================================================================#
#                        INSTALL DB CLIENT                           #
#====================================================================#
RUN apt-get -y install --fix-missing --no-install-recommends \
    mariadb-client \
    postgresql-client

#====================================================================#
#                         INSTALL SENDMAIL                           #
#====================================================================#
RUN apt-get install -q -y ssmtp mailutils

RUN line=$(head -n 1 /etc/hosts) \
    && line2=$(echo $line | awk '{print $2}') \
    && echo "$line $line2.localdomain" >> /etc/hosts \
    && apt install --fix-missing -y sendmail sendmail-cf m4 \
    && hostname >> /etc/mail/relay-domains \
    && m4 /etc/mail/sendmail.mc > /etc/mail/sendmail.cf \
    && sed -i -e "s/Port=smtp,Addr=127.0.0.1, Name=MTA/Port=smtp, Name=MTA/g" \
    /etc/mail/sendmail.mc \
    && sendmail -bd

#====================================================================#
#                        INSTALL PHP-IMAGIK                          #
#====================================================================#
RUN apt-get update && apt-get install --fix-missing -y \
    libmagickwand-dev --no-install-recommends \
    && pecl install imagick \
    && docker-php-ext-enable imagick

#====================================================================#
#                       INSTALL GIS LIBRARIES                        #
#====================================================================#
#gdal with ecw and libkml support
## First remove gdal if it's already installed
RUN apt remove -y gdal-bin gdal-data libgdal20 && \
    apt -y autoremove && \
    apt update && apt -y upgrade && \
    apt install -y libpng-dev libgdal-dev

## Unzip ECW libraries "Desktop Read-Only Redistributable"
COPY asset/ecw/hexagon.zip /root
RUN cd /root && \
    unzip hexagon.zip

## Copy new libraries to system folder
## Rename the newabi library as x64 and move necessary libraries to /usr/local/lib
RUN cp -r /root/hexagon/ERDAS-ECW_JPEG_2000_SDK-5.5.0/Desktop_Read-Only /usr/local/hexagon && \
    rm -r /usr/local/hexagon/lib/x64 && \
    mv /usr/local/hexagon/lib/cpp11abi/x64 /usr/local/hexagon/lib/x64 && \
    cp /usr/local/hexagon/lib/x64/release/libNCSEcw* /usr/local/lib && \
    ldconfig /usr/local/hexagon

## Install libspatialite
RUN apt-get update -y && \
    apt-get install --fix-missing -y \
    libspatialite-dev \
    sqlite3

## Install PROJ 8
COPY asset/ecw/proj-8.2.0.tar.gz /root
RUN cd /root && \
    tar xfvz proj-8.2.0.tar.gz && \
    cd proj-8.2.0 && \
    ./configure --prefix /usr/local && \
    make -j2 && \
    make install

## Install libkml
COPY asset/ecw/install-libkml-r864-64bit.tar.gz /root
RUN cd /root && \
    tar xzf install-libkml-r864-64bit.tar.gz && \
    cp -r install-libkml/include/* /usr/local/include && \
    cp -r install-libkml/lib/* /usr/local/lib

## Install libavif
RUN apt install --fix-missing -y libavif-dev

## Build GDAL with ECW and libkml support
COPY asset/ecw/gdal340.zip /root
RUN cd /root && \
    unzip gdal340.zip && \
    cd gdal-3.4.0 && \
    ./configure \
    --with-avif \
    --with-ecw=/usr/local/hexagon \
    #	--with-libkml=/usr/local/lib \
    --with-proj=/usr/local \
    --with-libtiff \
    --with-libz=internal \
    --with-png=internal \
    --with-geotiff=internal \
    --with-threads \
    --without-libkml \
    && \
    make clean && \
    make && \
    make install

## Check if it works
RUN export PATH=/usr/local/bin:$PATH && \
    export LD_LIBRARY_PATH=/usr/local/lib:$LD_LIBRARY_PATH && \
    gdalinfo --version && \
    gdalinfo --formats | grep ECW

## Remove installation files
RUN rm -rf /root/hexagon/ && \
    rm -rf /root/hexagon.zip && \
    rm -rf /root/proj-8.2.0/ && \
    rm -rf /root/proj-8.2.0.tar.gz && \
    rm -rf /root/install-libkml/ && \
    rm -rf /root/install-libkml-r864-64bit.tar.gz && \
    rm -rf /root/gdal-3.4.0/ && \
    rm -rf /root/gdal340.zip

#====================================================================#
#                          INSTALL MAPSERVER                         #
#====================================================================#
RUN apt-get -y install --fix-missing --no-install-recommends \
    libmapserver2 \
    fontconfig \
    cgi-mapserver \
    mapserver-bin \
    libopenjp2-7-dev \
    xl2tpd \
    strongswan \
    libapache2-mod-fcgid \
    libfreetype6

## Check if it works
RUN mapserv -v

RUN apt-get install --fix-missing -y libpq-dev
RUN apt-get install --no-install-recommends -y libpq-dev
RUN apt-get install -y libxml2-dev libbz2-dev zlib1g-dev

RUN apt-get install --fix-missing -y libsqlite3-dev \
    libsqlite3-0 \
    exif \
    ftp \
    ntp \
    gdal-bin

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions amqp ast bcmath bz2 calendar csv dba decimal ds enchant ev event excimer exif ffi \
    geospatial gettext gd gmp gnupg grpc http igbinary imap intl inotify \
    json_post ldap lzf mailparse maxminddb mcrypt memcache memcached mongodb msgpack mysqli oauth oci8 odbc opcache opencensus \
    openswoole pcov pdo_dblib pdo_firebird pdo_oci pdo_odbc pdo_mysql pdo_pgsql pdo_sqlsrv pcntl pgsql \
    pspell raphf redis seaslog shmop smbclient snmp \
    soap sockets ssh2 sqlsrv uuid xmldiff xmlrpc xsl \
    yac yaml yar zephir_parser zip zend_test zstd

#====================================================================#
#                           APACHE CONF                              #
#====================================================================#
COPY asset/ssl.conf /etc/apache2/mods-available/ssl.conf
COPY asset/security.conf /etc/apache2/conf-available/security.conf
COPY asset/000-default.conf /etc/apache2/sites-enabled/000-default.conf

#====================================================================#
#                       INSTALL COMPOSER 2.0                         #
#====================================================================#
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer self-update --2

#ENV APACHE_DOCUMENT_ROOT /var/www/html/codeigniter4/public
RUN  apt-get update && apt-get install -y ca-certificates gnupg
RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash -

#RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
#RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN /usr/sbin/a2enmod rewrite && /usr/sbin/a2enmod headers && /usr/sbin/a2enmod expires
RUN apt-get update && apt-get install -y libzip-dev zip && docker-php-ext-install zip
RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN apt-get install -y libtidy-dev \

    RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN pecl install xdebug

RUN echo 'zend_extension=xdebug' >> /usr/local/etc/php/php.ini
RUN echo 'xdebug.mode=develop,debug' >> /usr/local/etc/php/php.ini
RUN echo 'xdebug.client_host=host.docker.internal' >> /usr/local/etc/php/php.ini
RUN echo 'xdebug.start_with_request=trigger' >> /usr/local/etc/php/php.ini
RUN echo 'xdebug.client_port=9003' >> /usr/local/etc/php/php.ini
RUN echo 'session.save_path = "/tmp"' >> /usr/local/etc/php/php.ini

#====================================================================#
#                             ENABLE SSL                             #
#====================================================================#
RUN a2enmod ssl

#====================================================================#
#                           ENABLE MAPSERVER                         #
#====================================================================#
RUN	a2enmod cgi

#====================================================================#
#                        ENABLE MODULE HEADERS                       #
#====================================================================#
RUN a2enmod headers proxy_http

#====================================================================#
#                             INSTALL FOR                            #
#====================================================================#
RUN apt install -y nano udev dmidecode \
    && echo "www-data        ALL=(ALL) NOPASSWD: /usr/sbin/dmidecode"  | sudo tee /etc/sudoers.d/dont-prompt-www-data-for-sudo-password \
    && echo "www-data        ALL=(ALL) NOPASSWD: /etc/init.d/sendmail" | sudo tee -a /etc/sudoers.d/dont-prompt-www-data-for-sudo-password

#====================================================================#
#                             START SCRIPT                           #
#====================================================================#
COPY startScript.sh /startScript.sh

#====================================================================#
#                      CREATE GROUP AND USER                         #
#====================================================================#
RUN groupadd -r ${USER} && useradd -g ${USER} ${USER}

#====================================================================#
#                   SET OWNERCHIP AND PERMISSION                     #
#====================================================================#
RUN chown -R www-data:www-data /var/www/html

#====================================================================#
#                            CLEAN SYSTEM                            #
#====================================================================#
RUN apt-get clean && rm -r /var/lib/apt/lists/* \
    &&  rm -rf \
    /tmp/* \
    /root/.cache

#====================================================================#
#                              LOGS                                  #
#====================================================================#
RUN ln -sf /proc/self/fd/1 "/var/log/apache2/access.log" \
    && ln -sf /proc/self/fd/2 "/var/log/apache2/error.log" \
    && ln -sfT /dev/stdout "/var/log/apache2/access.log" \
    && ln -sfT /dev/stderr "/var/log/apache2/error.log" \
    && chown -R --no-dereference "www-data:www-data" "/var/log/apache2"

#====================================================================#
#                         SWITH TO USER                              #
#====================================================================#
#USER ${USER}
USER root

#====================================================================#
#                          SET WORKDIR                               #
#====================================================================#
WORKDIR /var/www/html/

#====================================================================#
#                          EXPOSE PORTS                              #
#====================================================================#
EXPOSE 80
EXPOSE 443

#====================================================================#
#                            VOLUMES                                 #
#====================================================================#
#VOLUME ["/var/www/html", "/usr/lib/php/20190902", "/etc/apache2", "/etc/php"]

#====================================================================#
#                           HEALTHCHECK                              #
#====================================================================#
HEALTHCHECK --interval=30s --timeout=3s --retries=5 CMD curl -f http://localhost/ || exit 1

#====================================================================#
#                            ENTRYPOINT                              #
#====================================================================#
CMD ["bash", "/startScript.sh"]