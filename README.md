# LEManager

Simple containerized web application for managing, issuing certificates (and email alerting) from Let's Encrypt certificate authority.

## Installation

1. direct domain like **cert.example.com** to your server where LEManager going to be installed

2. download docker container and run it:

    ```console
    ~# docker run --name lemanager \
      -v */certificates_dir*:/data \ 
      -e "HTTP_PASSWORD=*your_password*" \
      -e "HOSTNAME=*cert.example.com*" \
      -p *80*:80 \
      analogic/lemanager
    ```

   If you have port 80 in use you can use your existing webserver as reverse proxy (see NGiNX snippet).

3. go to *http://cert.example.com*, login with username *admin* and password *your_password*, setup "Email alerts settings" and issue new cert for *example.com* 

4. in folder */certificates_dir/example.com* you should find new certificate if everything goes ok. Add certificate to your webserver. NGiNX example:

    ```
    server {
        listen       443 ssl http2;
        server_name  example.com;
        
        **ssl_certificate */certificates_dir/example.com*/fullchain.pem;
        ssl_certificate_key */certificates_dir/example.com*/private.pem;
        ssl_trusted_certificate */certificates_dir/example.com*/fullchain.pem;**
        
        add_header Strict-Transport-Security "max-age=31536000; includeSubdomains;";
        
        ...
    }
    ```

5. reload your webserver with something like: *service nginx reload* or *killall -HUP nginx*. For doing reloads regularly when certificates automaticly renews you might find handy incrond which watch changes of filesystem and exec defined command. Or simply ad reload/HUP command to your daily/weekly cron. LEManager renews certificate every day at 1:01 after 14 days of its existence.

## NGiNX snippet for proxiing challanges only

```
server {
    listen       80;
    server_name  example2.com www.example2.com;

    location ^~ /.well-known {
        proxy_pass http://<container_host>:<container_port_80>;
    }

    ; redirect to https version if you need that
    location / {
        return 301 https://$server_name$request_uri;
    }

    ...
}
```

### Screenshots

![LEManager screenshot 0](https://github.com/analogic/lemanager/raw/master/web/images/screenshot-0.png)
![LEManager screenshot 1](https://github.com/analogic/lemanager/raw/master/web/images/screenshot-1.png)
![LEManager screenshot 2](https://github.com/analogic/lemanager/raw/master/web/images/screenshot-2.png)
![LEManager screenshot 3](https://github.com/analogic/lemanager/raw/master/web/images/screenshot-3.png)

## Why i created LEManager?

Because of implementation of Let's Encrypt to [Poste.io](https://poste.io)!
