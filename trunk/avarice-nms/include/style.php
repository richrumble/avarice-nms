<?php header('Content-type: text/css');
include_once(realpath(str_replace("D:", "", dirname(__FILE__))) . "/config.php");
?>
body {
  margin: 30px 0 20px 0;
  padding: 0;
  background: #D7D7BD url(<?php print CONF_BaseURL; ?>/images/img01.png) repeat;
  text-align: justify;
  line-height: 20px;
  font-family: Tahoma, Arial, Helvetica, sans-serif;
  font-size: 11px;
  color: #5a5f63;
}

h1, h2, h3 {
  padding: 0;
  margin: 0;
  color: #5a5f63;
}

h1 {
  font-size: 18px;
  font-weight: bold;
}

h3 {
  font-size: 12px;
  font-weight: bold;
}

img {
  border: none;
}

strong {
  color: #000000;
}

p, ul, ol {
  margin-bottom: 1.5em;
}

ul {
  margin: 0px;
  padding: 0;
}

a {
  color: #308AC0;
  border-bottom: 1px #97C8E5 dotted;
}

a:hover {
  text-decoration: none;
  color: #005880;
}

/** Style for header starts here */

#header {
  width: 1000px;
  height: 80px;
  margin: 0px auto;
  background: #5a5f63 url(<?php print CONF_BaseURL; ?>/images/ET_ProductOps_Banner.jpg) no-repeat left top;
}

#header h1, #header h2 {
  margin: 0px;
  padding: 0;
  text-transform: uppercase;
  font-family: Arial, Helvetica, sans-serif;
  font-weight: bold;
  color: #FFFFFF;
}

#header h1 {
  float: left;
  padding: 70px 10px 10px 20px;
  font-size: 3em;
}

#header h2 {
  padding: 73px 10px 0px 0px;
  font-size: 1.4em;
}

/** Style for menu starts here */

#menu {
  width: 1000px;
  height: 50px;
  margin: 0px auto;
  background: #000000;
}

#menu ul {
  margin: 0px;
  padding: 0 0 0 20px;
  list-style: none;
  float: right;
}

#menu li {
  display: inline;
}

#menu a {
  display: block;
  float: left;
  border: none;
  background: url(<?php print CONF_BaseURL; ?>/images/img05.jpg) no-repeat left 55%;
  padding: 14px 30px 10px 12px;
  text-decoration: none;
  color: #FFFFFF;
}

#menu a:hover {
  color: #f6a929;
  background: url(<?php print CONF_BaseURL; ?>/images/img05_hov.jpg) no-repeat left 55%;
}

#adminmenu {
  width: 1000px;
  height: 30px;
  margin: -20px 40px 20px -20px;
  padding-bottom: 15px;
  background: #000000;
}

#adminmenu ul {
  margin: 0px;
  padding: 0 0 0 20px;
  list-style: none;
  float: right;
}

#adminmenu li {
  display: inline;
}

#adminmenu a {
  display: block;
  float: left;
  border: none;
  background: url(<?php print CONF_BaseURL; ?>/images/img05.jpg) no-repeat left 55%;
  padding: 14px 30px 10px 12px;
  text-decoration: none;
  color: #FFFFFF;
}

#adminmenu a:hover {
  color: #f6a929;
  background: url(<?php print CONF_BaseURL; ?>/images/img05_hov.jpg) no-repeat left 55%;
}

/** Style for wrapper starts here */

#wrapper {
  width: 1040px;
  margin: 0px auto;
  padding: 20px 0;
  background: #FFFFFF;
}

/** Style for content starts here */

#content {
  width: 1000px;
  margin: 0px auto;
}

#content h2 {
  text-transform: uppercase;
  font-family: Arial, Helvetica, sans-serif;
  font-size: 14px;
  font-weight: bold;
  border-bottom: 1px #CCCCCC dashed;
}

#content p {
}

#content a {
  text-decoration: none;
}

#whole {
  width: 960px;
  padding: 20px 20px;
  background: #FFFFFF;
}

#right {
  float: right;
  width: 700px;
  padding: 20px 20px;
}

#right ul {
  margin: 0 3em;
  padding: 0;
  list-style: none;
}

#right li {
  margin-bottom: 8px;
  padding-left: 10px;
  background: url(<?php print CONF_BaseURL; ?>/images/img06.jpg) no-repeat 0 7px;
  line-height: 17px;
}

table.tabular {
  border: 1px solid #5a5f63;
  border-collapse: collapse;
  border-spacing: 0px;
  width: 100%;
}

table.tabular td {
  text-align: right;
  border: 1px solid #5a5f63;
  padding: 2px 2px;
  margin: 0px 0px;
}

table.tabular th {
  text-align: center;
  border: 2px solid #5a5f63;
  padding: 2px 2px;
  margin: 0px 0px;
}

table.tabular th.total {
  text-align: left;
  border-top: 1px solid #000000;
  border-bottom: 1px solid #000000;
  border-left: 1px solid #000000;
  padding: 2px 5px;
  margin: 0px 0px;
}

form.twocolumn br {
  clear: both;
}

form.twocolumn input select {
  display: inline;
}

form.twocolumn label {
  display: inline;
  float: left;
  width: 100px;
  padding-right: 5px;
}

form.twocolumnsmall br {
  clear: both;
}

form.twocolumnsmall input select {
  display: inline;
}

form.twocolumnsmall label {
  display: inline;
  float: left;
  width: 30px;
  padding-right: 5px;
}

input.button {
  border: 2px solid #5a5f63;
  background: #f6a929;
  font-weight: bolder;
  color: #5a5f63;
}

input.button:hover {
  border: 2px solid #f6a929;
  background: #5a5f63;
  font-weight: bolder;
  color: #f6a929;
}

#left {
  float: left;
  width: 220px;
  padding: 20px 20px;
  background: #EDEDED;
}

#left-content {
  float: left;
  width: 215px;
}

#left ul {
  margin: 15px 0 0 0;
  padding: 0;
  list-style: none;
}

#left li {
  padding-left: 10px;
  background: url(<?php print CONF_BaseURL; ?>/images/img04.gif) no-repeat left 50%;
}


/** Style for footer starts here */

#footer {
  clear: both;
  width: 1000px;
  height: 50px;
  margin: 0px auto;
  padding-top: 18px;
  border-top: 1px solid #444444;
  background: #000000;
  text-transform: uppercase;
  font-size: 10px;
  color: #E5E5CC;
}

#footer .copyright {
  text-transform: none;
  font-size: 85%;
  width: 60%;
  float: right;
  margin: 0px;
  padding-left: 20px;
  padding-right: 80px;
  text-align: center;
}

#footer .links {
  float: right;
  margin: 0px;
  padding-right: 20px;
  text-align: center;
}

#footer .css {
  padding-left: 20px;
  background: url(<?php print CONF_BaseURL; ?>/images/image01.png) no-repeat left 50%;
}

#footer .xhtml {
  padding-left: 20px;
  background: url(<?php print CONF_BaseURL; ?>/images/image02.png) no-repeat left 50%;
}

#footer a {
  border-bottom: 1px #E5E5CC dashed;
  text-decoration: none;
  color: #E5E5CC;
}

div.rssitem {
  border: 1px #000000 solid;
  padding: 5px;
  margin-bottom: 5px;
}

font.rsstitle {
  font-weight: bold;
}
