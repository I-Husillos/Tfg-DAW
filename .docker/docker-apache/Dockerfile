FROM httpd:latest

# 1. Habilitamos los módulos necesarios para PHP y reescritura de URLs (Laravel)
# 2. Habilitamos la inclusión de archivos de configuración extra
RUN sed -i \
    -e 's/^#\(LoadModule proxy_module modules\/mod_proxy.so\)/\1/' \
    -e 's/^#\(LoadModule proxy_fcgi_module modules\/mod_proxy_fcgi.so\)/\1/' \
    -e 's/^#\(LoadModule rewrite_module modules\/mod_rewrite.so\)/\1/' \
    -e 's/^#\(Include conf\/extra\/httpd-vhosts.conf\)/\1/' \
    conf/httpd.conf