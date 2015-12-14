<?php

include("_config.php");

$title = "Lemanager help";
include("_header.php");

?>

<div class="head">
    <h1>Lemanager help</h1>
    <p><a href="index">&larr; Back to the certificates list</a></p>
</div>

<div class="card">
    <h3>How Lemanager works?</h3>
    <p>Cooperation of Lemanager with Let's encrypt entity and your webserver can be simplified to following diagram:</p>
    <img src="images/basic-diagram.svg" />
</div>

<h2>Server setup</h2>

<div class="card">

    <h3>Knowledge requirements</h3>
    <ul>
        <li>Docker volumes</li>
        <li>Exposing ports in docker</li>
        <li>Nginx or Apache setup</li>
    </ul>

    <hr />

    <h3>Option 1 (best, not always possible)</h3>

    <p>
        <img src="images/option-1.svg" />
    </p>

    <p>By default Lemanager redirects all "normal" requests to HTTPS (port 443), so you will be serving only content secured by TLS. There is no need for proxying in webserver (besides to lemanager if you want).</p>

    <hr />

    <h3>Option 2 (integration to existing infrastructure)</h3>

    <p><img src="images/option-2.svg" /></p>

    <p>All requests are intercepted by your own webserver only requests to "/.well-known/..." and Lemanager domain are proxied to Lemanager container. See <a href="#proxy-config">webserver proxy configuration.</a></p>

    <hr />

    <h3>Option 3</h3>
    <p>More boxes or usage of NAS for exchanging certificates is ofcourse possible! You must keep in mind two tasks you need to ensure to work.</p>
    <ol>
        <li>Proxying requests from your webserver to Lemanager</li>
        <li>Sharing certificate files with your webserver</li>
        <li>(reloading webserver, see later)</li>
    </ol>
</div>

<h2>Webserver as proxy</h2>

<div class="card">
    <a name="proxy-config"></a>
    <h4>Example for NGINX</h4>
    <pre><code>server {
    listen       80;
    server_name  example.com www.example.com;

    <strong>location ^~ /.well-known {
        proxy_pass http://&lt;container_host>:&lt;container_port_80>;
    }</strong>

    ; redirect to https version if you need that
    location / {
        return 301 https://$server_name$request_uri;
    }

    ...

}</code></pre>

    <h3>Example for Apache</h3>
    <pre><code>&lt;VirtualHost *>
    ServerName example.com

    <strong>ProxyPreserveHost On
    ProxyPass /.well-known http://&lt;container_host>:&lt;container_port_80>/.well-known
    ProxyPassReverse /.well-known http://&lt;container_host>:&lt;container_port_80>/.well-known</strong>

    ...

&lt;/VirtualHost></code></pre>
</div>

<div class="card">
    <h2>Reloading webserver certificates</h2>
    <p>Since Lemanager is separated from your main webserver there is no mechanism to handle loading renewed certificates. You have following options depending on your environment:</p>
    <ol>
        <li>Use <a href="http://inotify.aiken.cz/?section=incron&page=about&lang=en">incron</a> to watch certificates directory (only local) and reload accordingly to changes with HUP signal/reload</li>
        <li>Reload every day/week/month with cron. Old certificates are valid until expiration so there is no need to reload as soon as possible.</li>
        <li>Custom solution based on cron&find detecting changes</li>
    </ol>
</div>

<?php
include("_footer.php");