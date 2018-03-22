# Changelog

v1.11
-----
- Updated `layout.html.twig` to limit overriding files and promote the use of Twig variable `display` (22/03/2018)
- Removed `fragments/header-pdf.html.twig` and `footer.html.twig` [BC-Break] (22/03/2018)
- Updated `README.md` (22/03/2018)

v1.10
-----
- Added VAT config value (21/03/2018)
- Added info about ToS in `README.md` (21/03/2018)

v1.9.3
------
- Modified label for Terms of Sales acceptance (21/03/2018)
- Added mandatory field (21/03/2018)

v1.9.2
------
- Added `proposedCurrencies` config value to allow only a set of currencies when creating Gift-Voucher (21/03/2018)

v1.9.1
------
- Added `returnRoute` to paymentData as it has changed in c975LPaymentBundle (20/03/2018)
- Renamed Route `payment_done` (20/03/2018)
- Added `setFinished(true)` to payment when GiftVoucher has been bought (20/03/2018)
- Redirect to Route `payment_display` in place of NotFound if Route `giftvoucher_payment_done` is called again after payment finished (20/03/2018)

v1.9
----
- Removed unused data in `GiftVoucherService` (18/03/2018)
- Corrected `offerLink` template (19/03/2018)
- Added userId if exists to payment data (19/03/2018)
- Added missing es translation (19/03/2018)
- Removed `action` property on Entity `GiftVoucherAvailable` and passed data with array `giftVoucherConfig` to the form (19/03/2018)
- Added Currency selector on creation of GiftVoucher (19/03/2018)
- Set currency to be uppercase in DB and Entities `GiftVoucherAvailable` and `GiftVoucherPurchased` (19/03/2018)

v1.8
----
- Changed TermsOfSales pdf filename to a translated one (13/03/2018)
- Added a global view to display Gift-Voucher to user (13/03/2018)
- Changed wording for offer link and button (13/03/2018)
- Added link to select2 library (13/03/2018)
- Added Twig extension to display some Gift-Vouchers (14/03/2018)
- Added explanations on `README.md` (14/03/2018)

v1.7
----
- Added "_locale requirement" part for multilingual prefix in `routing.yml` in `README.md` (04/03/2018)
- Added `live` config value (05/03/2018)
- Added mention of test if Gift-Vouchers are not live (05/03/2018)
- Added field `order_id` to store payment order_id (05/03/2018)
- Added link to payment via orderId (Need to update database table) (05/03/2018)
- Corrected display of Qrcode in email on xs devices (05/03/2018)
- Modified the wording of "Use Gift-Voucher" that may be confusing to users (05/03/2018)
- Re-ordered Gift-Voucher purchased data (05/03/2018)
- Moved "made by" mention from footer to under data panel (05/03/2018)
- Added config value `tos` to be approved when buying Gift-Voucher (06/03/2018)
- Added config value `tos_pdf` to be sent in email with Gift-Voucher (06/03/2018)
- Corrected `giftVoucher.es.xlf` (06/03/2018)
- Added `h4cc/wkhtmltopdf-amd64` to `composer.json` (07/03/2018)
- Added checkbox to approve the Terms of sales (07/03/2018)
- Corrected layout to include the content of css for the pdf (07/03/2018)
- Created method `Service > getIdentifierFormatted()` and made Twig extension to call it (to have it formatted in only one place) (08/03/2018)
- Grouped all display GiftVoucher templates under `display.html.twig` and removed the unused ones (08/03/2018)

v1.6.1
------
- Removed the "|raw" for `toolbar_button` call as safe html is now sent (01/03/2018)
- Added 'is_safe' to Twig extensions `GiftVoucherOfferButton` and `GiftVoucherOfferLink` to remove "|raw" on each call (01/03/2018)

v1.6
----
- Added c957L/IncludeLibrary to include libraries in layout.html.twig (27/02/2018)

v1.5
----
- Updated `README.md` for package name (21/02/2018)
- Abandoned Glyphicon and replaced by fontawesome (22/02/2018)

v1.4.1
------
- Updated `README.md` for package name (20/02/2018)

v1.4
----
- Changed composer folder name (20/02/2018)

v1.3
----
- Added possibilty to style offer button (19/02/2018)
- Removed `<pre></pre>` as they get copied withe code (19/02/2018)
- Reduced name of Twig function to get button/link from 'gift_voucher_' to 'gv_' (19/02/2018)

v1.2
----
- Updated ToolbarBundle product -> dashboard (05/02/2018)
- Updated Route `payment_display`  to `payment_form` (05/02/2018)
- Updated Route `payment_order`  to `payment_confirm` (05/02/2018)
- Re-ordered `GiftVoucherController.php` to have method in "order of logical use" (08/02/2018)
- Added of check if exist for the QrCode (08/02/2018)
- Renamed `getIdentifier()` to `getIdentifier()` as there is no numbers in the identifier (08/02/2018)
- Renamed DB field `number` to `identifier` as there is no numbers in the identifier (08/02/2018)
- Renamed "Purchased" to "Purchased" more logical (17/02/2018)
- Suppression of translation terms already present in `c975L/ToolbarBundle` (17/02/2018)
- Changed `valid` field to store DateInterval value (17/02/2018)
- Moved main div from templates to layout.html.twig (17/02/2018)
- Renamed 'edit' to modify' (17/02/2018)
- Renamed 'Purchase' to 'Offer' (17/02/2018)
- Changed data used to describe payment (18/02/2018)
- Added help pages (18/02/2018)

v1.1
----
- Remove of .travis.yml as tests have to be defined before (18/07/2017)
- Add of Bundle files (04/02/2018)
- Add support in `composer.json`+ use of ^ for versions request (04/02/2018)

v1.0.1
------
- Update of `composer.json` (08/07/2017)

v1.0
----
- Creation of bundle (08/07/2017)