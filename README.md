Example of config
=================

```
<?php
$akhethosts=array(
  "<id of the akhet host>" => array(
    "display_name" => "<name to display>",
    "api_hostname" => "<hostname of the akhet backend>",
    "api_username" => "<api username for akhet backend>",
    "api_password" => "<api password for akhet backend>",
    "api_protocol" => "<http or https to use for akhet backend>",
    "acl" => array(
      "requirelogin" => <true or false to require a login to use this host>,
      "requiregroups" => array(
        "<list of group required to use this host>"
        "sysop",
        "user",
      ),
      "requirematch" => array(
        "maildomain" => array(
          "<list of email domain name allowed to use this host>,
          "wikitolearn.org",
          "akhet.cc",
        ),
      ),
    ),
  ),
);
```
