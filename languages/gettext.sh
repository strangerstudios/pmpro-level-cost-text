#---------------------------
# This script generates a new pmpro-level-cost-text.pot file for use in translations.
# To generate a new pmpro-level-cost-text.pot, cd to the main /pmpro-level-cost-text/
# directory, then execute `languages/gettext.sh` from the command line.
# then fix the header info (helps to have the old .pot open before running script above)
# then execute `cp languages/pmpro-level-cost-text.pot languages/pmpro-level-cost-text.po` to copy the .pot to .po
# then execute `msgfmt languages/pmpro-level-cost-text.po --output-file languages/pmpro-level-cost-text.mo` to generate the .mo
#---------------------------
echo "Updating pmpro-level-cost-text.pot... "
xgettext -j -o languages/pmpro-level-cost-text.pot \
--default-domain=pmpro-level-cost-text \
--language=PHP \
--keyword=_ \
--keyword=__ \
--keyword=_e \
--keyword=_ex \
--keyword=_n \
--keyword=_x \
--sort-by-file \
--package-version=1.0 \
--msgid-bugs-address="info@paidmembershipspro.com" \
$(find . -name "*.php")
echo "Done!"
