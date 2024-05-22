<?php

/** @var string $content */
?>


<!--<!DOCTYPE html>-->
<html lang="en">

<head>
    <title>
        <?= $this->params['title'] ?>
    </title>
    <meta data-n-head="1" charset="utf-8">

    <style>
        body {
            margin: 0;
            font-family: "Cabin", sans-serif;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #71748d;
            text-align: left;
            background-color: white;
        }

        *,
        :after,
        :before {
            box-sizing: border-box;
        }

        #__nuxt {
            font-size: 0.875rem;
        }

        .min-vh-100 {
            min-height: 100vh !important;
        }

        .w-100 {
            width: 100% !important;
        }

        .bg-white {
            background-color: #fff !important;
        }

        .py-4 {
            padding-bottom: 1.5rem !important;
            padding-top: 1.5rem !important;
        }

        .py-3 {
            padding-bottom: 1rem !important;
            padding-top: 1rem !important;

        }

        .min-vh-100 {
            min-height: 100vh !important;
        }

        .d-flex {
            display: flex !important;
        }

        .flex-column {
            flex-direction: column !important;
        }

        .align-items-center {
            align-items: center !important;
        }

        .justify-content-between {
            justify-content: space-between !important;
        }

        .col-6 {
            flex: 0 0 50%;
            max-width: 50%;
            position: relative;
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
        }

        .container {
            max-width: 1140px;
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto;
        }

        .b-table-sticky-header>.table,
        .table-responsive>.table,
        [class*=table-responsive-]>.table {
            margin-bottom: 0;
        }

        table {
            font-size: 0.875rem;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #71748d;
            line-height: 1.5;
            border-collapse: collapse;
        }

        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table td,
        .table th {
            padding: .75rem;
            vertical-align: middle;
            border-top: 1px solid #dee2e6;
        }

        tbody {
            display: table-row-group;
            vertical-align: middle;
            border-color: inherit;
        }

        tr {
            display: table-row;
            border-color: inherit;
        }

        th {
            text-align: left;
        }

        td {
            display: table-cell;
        }

        .text-right {
            text-align: right !important;
        }

        .foot-no-border td {
            border: 0;
        }

        .font-weight-bold {
            font-weight: 700 !important;
        }

        .text-center {
            text-align: center !important;
        }

        .mt-auto {
            margin-top: auto !important;
        }

        .text-uppercase {
            text-transform: uppercase !important;
        }

        .border-bottom {
            border-bottom: 1px solid #dee2e6 !important;
        }

        .border-top {
            border-top: 1px solid #dee2e6 !important;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: .5rem;
            font-family: "Cabin", sans-serif;
            font-weight: 400;
            line-height: 1.2;
            color: #3d405c;
            margin-top: 0;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Cabin:wght@500;700&display=swap" rel="stylesheet" />
</head>

<body>
    <?= $content; ?>
</body>

</html>