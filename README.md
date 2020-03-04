# GiftVoucherBundle


GiftVoucherBundle does the following:

- Allows to create Gift Voucher request form,
- Interfaces with Stripe via [c975LPaymentBundle](https://github.com/975L/PaymentBundle) for its payment,
- Creates a PDF of the GiftVoucher and sends it by email,
- Creates a QR Code using [QrCodeBundle](https://github.com/endroid/qr-code),
- Allows to use the GiftVoucher via a QrCode plus validation aftewards,
- Integrates with [c975LToolbarBundle](https://github.com/975L/ToolbarBundle),
- PDF and Qrcode are NOT stored but created on the fly,
- Joins your Terms of sales as PDF to the email,

**The security is provided by a four-letter secret code, included in the QrCode, but not in the displayed Gift-Voucher identifier.**

This Bundle relies on the use of [c975LPaymentBundle](https://github.com/975L/PaymentBundle), [Stripe](https://stripe.com/) and its [PHP Library](https://github.com/stripe/stripe-php).
**So you MUST have a Stripe account.**

It also recomended to use this with a SSL certificat to reassure the user.

As the Terms of sales MUST be sent to the user with the Gift-Voucher, you MUST provide a Route or url for this PDF file. If you don't have such, you may consider using [c975LSiteBundle](https://github.com/975L/SiteBundle) for its pre-defined models and [c975LPageEditBundle](https://github.com/975L/PageEditBundle) for its ability to create a PDF.

You can also give a better user's experience by using [Select2](https://select2.org) for the selection of GiftVoucher. Simply include it to your layout using

```twig
    {# jQuery has to be linked before #}
    {# In your css block #}
    {{ inc_lib('select2', 'css', '4.*') }}
    {# In your javascript block #}
    {{ inc_lib('select2', 'js', '4.*') }}
```

[GiftVoucherBundle dedicated web page](https://975l.com/en/pages/gift-voucher-bundle).

[GiftVoucherBundle API documentation](https://975l.com/apidoc/c975L/GiftVoucherBundle.html).

## Bundle installation

### Step 1: Download the Bundle

v3.x works with Symfony 4.x. **Use v2.x for Symfony 3.x**
Use [Composer](https://getcomposer.org) to install the library

```bash
    composer require c975l/giftvoucher-bundle
```

### Step 2: Configure the Bundle

Check dependencies for their configuration:

- [Symfony Mailer](https://github.com/symfony/mailer)
- [Doctrine](https://github.com/doctrine/DoctrineBundle)
- [KnpPaginatorBundle](https://github.com/KnpLabs/KnpPaginatorBundle)
- [KnpTimeBundle](https://github.com/KnpLabs/KnpTimeBundle)
- [QrCodeBundle](https://github.com/endroid/qr-code)
- [Stripe PHP Library](https://github.com/stripe/stripe-php).
- [c975LEmailBundle](https://github.com/975L/EmailBundle)
- [c975LPaymentBundle](https://github.com/975L/PaymentBundle)

c975LGiftVoucherBundle uses [c975L/ConfigBundle](https://github.com/975L/ConfigBundle) to manage configuration parameters. Use the Route "/gift-voucher/config" with the proper user role to modify them.

### Step 3: Enable the Routes

Then, enable the routes by adding them to the `/config/routes.yaml` file of your project:

```yml
c975_l_giftvoucher:
    resource: "@c975LGiftVoucherBundle/Controller/"
    type: annotation
    prefix: /
    #Multilingual website use the following
    #prefix: /{_locale}
    #defaults:   { _locale: '%locale%' }
    #requirements:
    #    _locale: en|fr|es
```

### Step 4: Create MySql tables

You can use `php bin/console make:migration` to create the migration file as documented in [Symfony's Doctrine docs](https://symfony.com/doc/current/doctrine.html) OR use `/Resources/sql/gift-voucher.sql` to create the tables `gift_voucher_available` and `gift_voucher_purchased`. The `DROP TABLE` are commented to avoid dropping by mistake.

### Step 5: Override templates

It is strongly recommended to use the [Override Templates from Third-Party Bundles feature](http://symfony.com/doc/current/templating/overriding.html) to integrate fully with your site.

For this, simply, create the following structure `/templates/bundles/c975LGiftVoucherBundle/` in your app and then duplicate the file `layout.html.twig` in it, to override the existing Bundle file.

In `layout.html.twig`, it will mainly consist to extend your layout and define specific variables, i.e. :

```twig
{% extends 'layout.html.twig' %}

{% block content %}
    {% block giftVoucher_content %}
    {% endblock %}
{% endblock %}```
```

Then in your `layout.html.twig` and its dependencies, such as `header.html.twig`, `footer.html.twig`, `navbar.html.twig`, etc. you can use the condition `if display == 'pdf'` or `if display == 'html'` to choose what to display to which format, and you keep all your data in one place.

**Keep in mind that links have to be absolute, or their content included, to be exported (see below).**

### Routes

The different Routes (naming self-explanatory) available are:

- giftvoucher_display
- giftvoucher_config
- giftvoucher_create
- giftvoucher_modify
- giftvoucher_duplicate
- giftvoucher_delete
- giftvoucher_dashboard
- giftvoucher_purchased
- giftvoucher_offer
- giftvoucher_offer_all
- giftvoucher_use
- giftvoucher_slug
- giftvoucher_help
- giftvoucher_qrcode

**You should use Route `giftvoucher_offer_all` as an entry point to your Gift-Vouchers.**

### Twig extensions

You can use the following Twig extensions to display Gift-Vouchers around your web site.

### `gv_offer_button()`

There are different ways to use this extension:

`{{ gv_offer_button(GIFTVOUCHER_AVAILABLE_ID) }}` will display a button with defaults styles
`{{ gv_offer_button(GIFTVOUCHER_AVAILABLE_ID, 'btn-primary') }}` will display a button with specified styles
`{{ gv_offer_button(GIFTVOUCHER_AVAILABLE_ID, 'WHATEVER_STYLE_YOU_HAVE_DEFINED_IN_CSS') }}` will display a button using your own styles

These codes, and other variants, are recalled on the display of Gift-Voucher for Admin users.

### `gv_offer_link()`

You will use this Twig extension to display a link to ofeer the Gift-Voucher

`{{ gv_offer_link(GIFTVOUCHER_AVAILABLE_ID) }}` will display a link

This code is recalled on the display of Gift-Voucher for Admin users.

### `gv_view_all()`

This Twig extension will create a view of your Gift-Vouchers. It is used on `Resources/views/pages/offerAll.html.twig` template, used by Route `giftvoucher_offer_all`.

`{{ gv_view_all() }}` will create the view with all your available Gift-Vouchers
`{{ gv_view_all(NUMBER_OF_GIFTVOUCHERS_TO_DISPLAY) }}` will create the view with the specified number of your available Gift-Vouchers
`{{ gv_view_all(NUMBER_OF_GIFTVOUCHERS_TO_DISPLAY, ORDERED_FIELD) }}` will create the view with the specified number of your available Gift-Vouchers, ordered by the specified field. Values for this field are the ones of the Database Table `gift_voucher_available`. You will mostly use `id`, `object` (default one), `slug`, `amount`.

If this project **help you to reduce time to develop**, you can sponsor me via the "Sponsor" button at the top :)
