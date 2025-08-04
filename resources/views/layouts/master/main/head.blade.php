 <?php
 $title = isset(setting()->meta_title) ? setting()->meta_title : 'CRM - Admin';
 $keywords = isset(setting()->meta_keywords) ? [setting()->meta_keywords] : [];
 $description = isset(setting()->meta_description) ? setting()->meta_description : null;
 $favicon = !empty(setting()->seo_image) ? setting()->seo_image : URL::asset('assets/images/favicon-32x32.png');
 ?>

 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1" />
 <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
 {{-- <meta name="format-detection" content="telephone=no" />
 <meta name="apple-mobile-web-app-capable" content="yes" /> --}}

 <link rel="icon" href="{{ $favicon }}" type="image/png" />
 <meta name="keywords" content="<?php foreach ($keywords as $value) {
     echo $value . ', ';
 } ?>">
 <meta content="{{ $description }}" name="description">
 <meta name="author" content="CRM - Admin">

 <meta name="csrf-token" content="{{ csrf_token() }}" />

 <title>@yield('title', $title)</title>
