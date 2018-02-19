# Changelog

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