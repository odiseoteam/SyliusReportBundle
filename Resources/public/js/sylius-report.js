/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
(function ($) {
    'use strict';

    $(document).ready(function() {
        $('#sylius_report_renderer').handlePrototypes({
            'prototypePrefix': 'sylius_report_renderer_renderers',
            'containerSelector': '#sylius_report_renderer_configuration'
        });
        $('#sylius_report_dataFetcher').handlePrototypes({
            'prototypePrefix': 'sylius_report_dataFetcher_dataFetchers',
            'containerSelector': '#sylius_report_dataFetcher_configuration'
        });
        $('#sylius_report_dataFetcher').on('change', function (e) {
            $('#sylius_report_dataFetcher_configuration .ui.toggle.checkbox').checkbox();
            $('#sylius_report_dataFetcher_configuration .ui.dropdown').dropdown();
        });
    });
})( jQuery );