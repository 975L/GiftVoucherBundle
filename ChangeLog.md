# Changelog

- Updated `README.md` (01/09/2018)
- Renamed `fragments/javascript.html.twig` (04/09/2018)

v2.0
----
**Upgrading from v1.x? Check UPGRADE.md**
- Created branch 1.x (31/08/2018)
- Updated composer.json (01/09/2018)
- Updated `README.md` (01/09/2018)
- Added `UPGRADE.md` (01/09/2018)
- Added `bundle.yaml` (01/09/2018)
- Removed declaration of parameters in Configuration class as they are end-user parameters and defined in c975L/ConfigBundle (01/09/2018)
- Added Route `giftvoucher_config` (01/09/2018)
- Removed calls of `$container->getParameter()` (01/09/2018)


v1.x
====

v1.15.5
-------
- Fixed Voter constants (31/08/2018)

v1.15.4.1
---------
- Used a `switch()` for the FormFactory more readable (27/08/2018)

v1.15.4
-------
- Added missing redirect if GiftVoucher purchase validation fails (25/08/2018)
- Removed 'true ===' as not needed (25/08/2018)
- Added dependency on "c975l/config-bundle" and "c975l/services-bundle" (26/08/2018)
- Deleted un-needed translations (26/08/2018)
- Removed un-needed Services (26/08/2018)
- Added GiftVoucherFormFactory + Interface (27/08/2018)
- Removed 'made with' link as it can lead to misunderstanding for users (27/08/2018)

v1.15.3.1
---------
- Replaced links in dashboard (for purchased) by buttons (25/08/2018)

v1.15.3
-------
- Suppressed link for copying code in Dashboard (25/08/2018)
- Replaced links in dashboard by buttons (25/08/2018)
- Corrected display for offer buttons in display available Gift-Voucher (25/08/2018)

v1.15.2
-------
- Made use of @ParamConverter for payment returnRoute (24/08/2018)

v1.15.1
-------
- Renamed `samples` file and calls (23/08/2018)
- Put javascript for forms in a separate file (23/08/2018)

v1.15
-----
- Added Voter for Slug Route (02/08/2018)
- Added link to BuyMeCoffee (23/08/2018)
- Added link to apidoc (23/08/2018)
- Removed FQCN (23/08/2018)
- Split GiftVoucherService in multiples files + creation of Interfaces (23/08/2018)
- Added display of the IP address in offer form to be GDPR compliant (23/08/2018)
- Added config option for GDPR (23/08/2018)
- Added checkbok fo GDPR on offer form (23/08/2018)
- Updated documentation (23/08/2018)
- Renamed `gv_samples` Twig Function to `gv_view_all` and `GiftVoucherSamples.php` to `GiftVoucherViewAll.php`as not good naming [BC-Break] (23/08/2018)
- Renamed `giftVoucherIdentifier` Twig filter to `gv_identifier` to be coherent with other naming [BC-Break] (23/08/2018)
- Made controllers skinny and re-ordered them (23/08/2018)
- Changed to 'warning' for Test use of GiftVoucherPurchased (23/08/2018)
- Added ParamConverter for Controller methods (23/08/2018)
- Added confirmation step for utilisation of GiftVoucherPurchased (23/08/2018)
- Removed id display in dashboard for GiftVoucherAvailable (23/08/2018)

v1.14.2
-------
- Update slugify method to check unicity of slug (02/08/2018)
- Renamed things link to `add` to `create` (02/08/2018)
- Ordered in alphabetical AvailableVoter constants (02/08/2018)
- Renamed Routes (02/08/2018)

v1.14.1
-------
- Renamed `forms/new.html.twig` to `forms/add.html.twig` (01/08/2018)

v1.14
-----
- Made use of TranslatorInterface (31/07/2018)
- Made use of Voters for access rights (01/08/2018)
- Merged `GiftVoucherController.php` to `AvailableController.php` more logical (01/08/2018)
- Renamed `new` to `add` to avoid using php reserved word (01/08/2018)
- Renamed `use` to `utilization` to avoid using php reserved word (01/08/2018)

v1.13.2
-------
- Injected `AuthorizationCheckerInterface` in Controllers to avoid use of `$this->get()` (30/07/2018)
- Made use of ParamConverter (30/07/2018)
- Injected `Translator` (30/07/2018)
- Removed toolbar display when not signed in (30/07/2018)
- Corrected toolbar in help file (30/07/2018)
- Corrected dashboard links (30/07/2018)

v1.13.1
-------
- Corrected PurchasedController (29/07/2018)

v1.13
-----
- Corrected offer Route (26/07/2018)
- Added Controller for auto-wire (26/07/2018)
- Corrected Service > sendEmail method (26/07/2018)
- Split controller files (27/07/2018)
- Moved to Endroid/QrCode ^3 (27/07/2018)

v1.12.1
-------
- Removed `SubmitType` in GiftVoucherAvailableType and replaced by adding button in template as it's not a "Best Practice" (Revert of v1.11.1) (21/07/2018)

v1.12
-----
- Removed required in composer.json (22/05/2018)
- Removed `Action` in controller method name as not requested anymore (21/07/2018)
- Corrected meta in `layout.html.twig` (21/07/2018)
- Use of Yoda notation (21/07/2018)

v1.11.2
-------
- Modified toolbars calls due to modification of c975LToolbarBundle (13/05/2018)

v1.11.1
-------
- Replaced submit button by `SubmitType` (16/04/2018)

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
