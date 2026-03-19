jQuery(function ($) {
  "use strict";

  /**
   * Object to handle Stripe admin functions.
   */
  var wc_stripe_admin = {
    isTestMode: function () {
      return $("#woocommerce_stripe_testmode").is(":checked");
    },

    getSecretKey: function () {
      if (wc_stripe_admin.isTestMode()) {
        return $("#woocommerce_stripe_test_secret_key").val();
      } else {
        return $("#woocommerce_stripe_secret_key").val();
      }
    },

    /**
     * Initialize.
     */
    init: function () {
      $(document.body).on(
        "change",
        "#woocommerce_stripe_testmode",
        function () {
          var test_secret_key = $("#woocommerce_stripe_test_secret_key")
              .parents("tr")
              .eq(0),
            test_publishable_key = $("#woocommerce_stripe_test_publishable_key")
              .parents("tr")
              .eq(0),
            test_webhook_secret = $("#woocommerce_stripe_test_webhook_secret")
              .parents("tr")
              .eq(0),
            live_secret_key = $("#woocommerce_stripe_secret_key")
              .parents("tr")
              .eq(0),
            live_publishable_key = $("#woocommerce_stripe_publishable_key")
              .parents("tr")
              .eq(0),
            live_webhook_secret = $("#woocommerce_stripe_webhook_secret")
              .parents("tr")
              .eq(0);

          if ($(this).is(":checked")) {
            test_secret_key.show();
            test_publishable_key.show();
            test_webhook_secret.show();
            live_secret_key.hide();
            live_publishable_key.hide();
            live_webhook_secret.hide();
          } else {
            test_secret_key.hide();
            test_publishable_key.hide();
            test_webhook_secret.hide();
            live_secret_key.show();
            live_publishable_key.show();
            live_webhook_secret.show();
          }
        }
      );

      $("#woocommerce_stripe_testmode").change();

      // Toggle Payment Request buttons settings.
      $("#woocommerce_stripe_payment_request")
        .change(function () {
          if ($(this).is(":checked")) {
            $(
              "#woocommerce_stripe_payment_request_button_theme, #woocommerce_stripe_payment_request_button_type, #woocommerce_stripe_payment_request_button_height"
            )
              .closest("tr")
              .show();
          } else {
            $(
              "#woocommerce_stripe_payment_request_button_theme, #woocommerce_stripe_payment_request_button_type, #woocommerce_stripe_payment_request_button_height"
            )
              .closest("tr")
              .hide();
          }
        })
        .change();

      // Toggle Custom Payment Request configs.
      $("#woocommerce_stripe_payment_request_button_type")
        .change(function () {
          if ("custom" === $(this).val()) {
            $("#woocommerce_stripe_payment_request_button_label")
              .closest("tr")
              .show();
          } else {
            $("#woocommerce_stripe_payment_request_button_label")
              .closest("tr")
              .hide();
          }
        })
        .change();

      // Toggle Branded Payment Request configs.
      $("#woocommerce_stripe_payment_request_button_type")
        .change(function () {
          if ("branded" === $(this).val()) {
            $("#woocommerce_stripe_payment_request_button_branded_type")
              .closest("tr")
              .show();
          } else {
            $("#woocommerce_stripe_payment_request_button_branded_type")
              .closest("tr")
              .hide();
          }
        })
        .change();

      // Make the 3DS notice dismissable.
      $(".wc-stripe-3ds-missing").each(function () {
        var $setting = $(this);

        $setting
          .find(".notice-dismiss")
          .on("click.wc-stripe-dismiss-notice", function () {
            $.ajax({
              type: "head",
              url:
                window.location.href +
                "&stripe_dismiss_3ds=" +
                $setting.data("nonce"),
            });
          });
      });
    },
  };

  wc_stripe_admin.init();

  $(document).ready(function () {
    RegisterEvents();
  });

  // Set up the Marketing 360 Sign In click trigger.
  function RegisterEvents() {
    $("#wc-m360-api-auth").click(m360SignIn);
  }

  // Bring up the prompt for the user to sign in to Marketing 360
  function m360SignIn(e) {
    e.preventDefault();

    const signInPopup = $("#wc-m360-signin-popup-wrap");
    const signInForm = $("#wc-m360-signin-popup-form-login");

    signInPopup
      .show()
      .off()
      .click(function (e) {
        if (e.target === this) {
          $(this).hide();
        }
      });

    signInForm.submit(onFormSubmit);
  }

  // Handle the form submission inside the prompt
  function onFormSubmit(e) {
    e.preventDefault();

    const form = e.target;

    const accountsList = $("#wc-m360-signin-popup-accounts-list");
    const accountsListSubHeading = $("#wc-m360-signin-popup-subtitle");
    const contentWrapper = $("#wc-m360-signin-popup-content-wrapper");

    $("#wc-m360-signin-popup-login").val("Connecting...");

    $.ajax({
      url: connectUrl,
      method: form.method,
      beforeSend: function (xhr) {
        xhr.setRequestHeader("X-WP-Nonce", nonce);
      },
      data: $(this).serialize(),
    })
      .done(function (response) {
        if (Array.isArray(response)) {
          contentWrapper.hide();
          accountsList.show();
          accountsListSubHeading.show();
          response.forEach(function (account) {
            const html = $(account.html);

            delete account.html;
            html.click(function () {
              $("#woocommerce_stripe_account_details").val(account.payload);
              $("#wc-m360-signin-popup-wrap").hide();
              contentWrapper.show();
              accountsList.hide();
              accountsListSubHeading.hide();

              const notice = `
							<p>Currently connected to Marketing 360® account: ${account.externalAccountNumber} ${account.displayName}. Please click "Save Changes" to enable payments. <a href="#" onclick="m360SignOut()">Disconnect Account</a></p>
						`;

              $("#wc-m360-notice-box").html(notice);
              $("#wc-m360-api-auth").text(
                "Connect to a different Marketing 360® account"
              );
            });
            accountsList.append(html);
          });
        }
      })
      .error(function (response) {
        $("#alert-error").text(response.responseText);

        $("#wc-m360-signin-popup-login").val("Connect");
        console.error(response);
      });
  }
});

function m360SignOut() {
  jQuery("#woocommerce_stripe_account_details").val("").change();
  const notice = `
		<p>You have disconnected from Marketing 360®. Please click "Save Changes" to finish disconnecting.</p>
	`;

  jQuery("#wc-m360-notice-box").html(notice);
  jQuery("#wc-m360-api-auth").text("Connect to Marketing 360®");
}
