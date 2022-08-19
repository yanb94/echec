vcl 4.0;

include "./fosHttpCache/fos_purge.vcl";
include "./fosHttpCache/fos_refresh.vcl";
include "./fosHttpCache/fos_ban.vcl";
include "./fosHttpCache/fos_tags_xkey.vcl";
include "./fosHttpCache/fos_user_context.vcl";
include "./fosHttpCache/fos_user_context_url.vcl";
include "./fosHttpCache/fos_custom_ttl.vcl";
include "./fosHttpCache/fos_debug.vcl";

backend default{
    .host = "apache";
    .port = "8000";
}

acl invalidators {
    "apache";
    "php-fpm";
}

sub vcl_recv {

    set req.http.cookie = ";" + req.http.cookie;
    set req.http.cookie = regsuball(req.http.cookie, "; +", ";");
    set req.http.cookie = regsuball(req.http.cookie, ";(PHPSESSID)=", "; \1=");
    set req.http.cookie = regsuball(req.http.cookie, ";[^ ][^;]*", "");
    set req.http.cookie = regsuball(req.http.cookie, "^[; ]+|[; ]+$", "");

    call fos_purge_recv;
    call fos_refresh_recv;
    call fos_ban_recv;
    call fos_tags_xkey_recv;
    call fos_user_context_recv;
}

sub vcl_backend_response {
    call fos_ban_backend_response;
    call fos_user_context_backend_response;
    call fos_custom_ttl_backend_response;
}

sub vcl_deliver {
    call fos_ban_deliver;
    call fos_tags_xkey_deliver;
    call fos_user_context_deliver;
    call fos_debug_deliver;
}