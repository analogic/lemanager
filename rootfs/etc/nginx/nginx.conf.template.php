worker_processes  1;

events {
    worker_connections  1024;
}

http {

    include /etc/nginx/http.conf;

    server {
        #
        # setting for redirecting all domains to https version - see "Option 1" in help
        #
        listen 80;
        root /opt/lemanager/web;

        location ^~ /.well-known {
            try_files $uri =404;
        }

        location / {
            return 301 https://$http_host$request_uri;
        }
    }

    server {
        #
        # Lemanager admin itself
        #
        listen 80;
        server_name <?php echo $hostname ?>;
        include /etc/nginx/lemanager.conf;
    }

<?php if(!empty($ssl)): ?>
    server {
        listen 443 ssl;
        server_name <?php echo $hostname ?>;

        ssl on;
        ssl_certificate <?php echo $ssl ?>/fullchain.pem;
        ssl_certificate_key <?php echo $ssl ?>/private.pem;

        include /etc/nginx/lemanger.conf;
    }
<?php endif ?>
}