<?php

namespace YOOtheme;

return [

    'transforms' => [

        'render' => function ($node) {
            /**
             * @var Metadata $metadata
             */
            $metadata = app(Metadata::class);
            $metadata->set('style:builder-whatsapp', ['href' => Path::get('./css/style.css')]);
        },
    ],

    // Define updates for the element node
    'updates' => [

        '1.18.0' => function ($node, array $params) {

            // Remove or modify deprecated properties
            unset($node->props['deprecated_prop']);

        },

    ],

];
