GiftVoucherBundle
=================

GiftVoucherBundle does the following:

- Allows to create Gift Voucher request form,
- Interfaces with Stripe via [c975LPaymentBundle](https://github.com/975L/PaymentBundle) for its payment,
- Creates a PDf, using [KnpSnappyBundle](https://github.com/KnpLabs/KnpSnappyBundle) and [wkhtmltopdf](https://wkhtmltopdf.org/), of the GiftVoucher and sends it by email (if requested),
- Creates a QR Code using [QrCodeBundle](https://github.com/endroid/qr-code),
- Allows to use the GiftVoucher via a QrCode plus validation aftewards,
- PDF and Qrcode are NOT stored but created on the fly.

The security is provided by a four-letter secret code, included in the QrCode, but not in the displayed Gift-Voucher identifier.

This Bundle relies on the use of [c975LPaymentBundle](https://github.com/975L/PaymentBundle), [Stripe](https://stripe.com/) and its [PHP Library](https://github.com/stripe/stripe-php).
**So you MUST have a Stripe account.**
It also recomended to use this with a SSL certificat to reassure the user.

[GiftVoucherBundle dedicated web page](https://975l.com/en/pages/gift-voucher-bundle).

Bundle installation
===================

Step 1: Download the Bundle
---------------------------
Use [Composer](https://getcomposer.org) to install the library
```bash
    composer require c975l/gift-voucher-bundle
```

Step 2: Enable the Bundles
--------------------------
Then, enable the bundles by adding them to the list of registered bundles in the `app/AppKernel.php` file of your project:

```php
<?php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...
            new Knp\Bundle\SnappyBundle\KnpSnappyBundle(),
            new Knp\Bundle\TimeBundle\KnpTimeBundle(),
            new c975L\EmailBundle\c975LEmailBundle(),
            new c975L\PaymentBundle\c975LPaymentBundle(),
            new c975L\GiftVoucherBundle\c975LGiftVoucherBundle(),
        ];
    }
}
```

Step 3: Configure the Bundle
----------------------------
Check [c975LEmailBundle](https://github.com/975L/EmailBundle) and [c975LPaymentBundle](https://github.com/975L/PaymentBundle) for their specific configuration.
You should also check [KnpSnappyBundle](https://github.com/KnpLabs/KnpSnappyBundle) for its configuration but below is a common set.
In the `app/config.yml` file of your project, define the following:

```yml
knp_snappy:
    process_timeout: 20
    temporary_folder: "%kernel.cache_dir%/snappy"
    pdf:
        enabled:    true
        binary:     "%kernel.root_dir%/../vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64"
        options:
            print-media-type: true
            page-size: A4
            orientation: 'portrait'
            encoding : utf-8
            dpi: 300
            images: true
            image-quality: 80
            margin-left: 15mm
            margin-right: 15mm
            margin-top: 15mm
            margin-bottom: 15mm
    image:
        enabled:    false

#GiftVoucherBundle
c975_l_gift_voucher:
    #The role needed to use a GiftVoucher
    roleNeeded: 'ROLE_ADMIN'
```

Step 4: Enable the Routes
-------------------------
Then, enable the routes by adding them to the `app/config/routing.yml` file of your project:

```yml
c975_l_giftvoucher:
    resource: "@c975LGiftVoucherBundle/Controller/"
    type:     annotation
    #Multilingual website use: prefix: /{_locale}
    prefix:   /
```

Step 5: Create MySql tables
---------------------------
- Use `/Resources/sql/gift-voucher.sql` to create the tables `gift_voucher_available` and `gift_voucher_purchased`. The `DROP TABLE` are commented to avoid dropping by mistake.

How to use
----------
You should override `Resources/layout-pdf.html.twig`, `Resources/fragments/header-pdf.html.twig` and `Resources/footer-pdf.html.twig`, to define your proper layout for the pdf export.
**Keep in mind that links have to be absolute to be exported.**

The different Routes (naming self-explanatory) available are:
- giftvoucher_display
- giftvoucher_display_available
- giftvoucher_new
- giftvoucher_modify
- giftvoucher_duplicate
- giftvoucher_delete
- giftvoucher_dashboard
- giftvoucher_offer
- giftvoucher_use
- giftvoucher_slug
- giftvoucher_help
- giftvoucher_qrcode

Problems
--------
Due to https://github.com/wkhtmltopdf/wkhtmltopdf/issues/3001, you may have to follow https://gist.github.com/kai101/99d57462f2459245d28b4f5ea51aa7d0 to enable https links.