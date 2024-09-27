let checkoutForm = {
    CARD_ID: "1",
    CASH_ID: "2",
    targetAmount: 14527,
    VARS: null,
    init(vars) {
        checkoutForm.VARS = vars;

        document.getElementById("transaction_form_amount").value = checkoutForm.targetAmount;

        $("#transaction_form_currency").select2({ width: 100 });
        $("#transaction_form_paymentMethod").select2({ width: 100 });

        let cashForm = document.getElementById('cashPaymentForm');
        let cardForm = document.getElementById('cardPaymentForm');

        checkoutForm.toggleFormInputs(cardForm, false);
        checkoutForm.toggleFormInputs(cashForm, true);

        checkoutForm.setupEventListeners();
        $("#transaction_form_paymentMethod").val(document.getElementById("transaction_form_paymentMethod").value).trigger('change');
    },

    setupEventListeners() {
        let cashForm = document.getElementById('cashPaymentForm');
        let cardForm = document.getElementById('cardPaymentForm');
        let totalAmountSection = document.getElementById('totalAmount');

        $('#transaction_form_paymentMethod').on('change', function () {
            checkoutForm.togglePaymentForms($(this).val(), cashForm, cardForm, totalAmountSection);
        });

        document.querySelectorAll('.coin-input').forEach(input => {
            input.addEventListener('input', checkoutForm.updateTotal);
        });

        document.getElementById("submitCash").addEventListener('click', checkoutForm.handleCashPayment);

        document.getElementById("submitCard").addEventListener('click', checkoutForm.handleCardPayment);

        document.getElementById('cardExpiry').addEventListener('input', checkoutForm.formatCardExpiry);
    },

    togglePaymentForms(selectedValue, cashForm, cardForm, totalAmountSection) {
        if (selectedValue === checkoutForm.CASH_ID) {
            cashForm.style.display = 'block';
            cardForm.style.display = 'none';
            totalAmountSection.style.display = 'flex';
            checkoutForm.toggleFormInputs(cardForm, false);
            checkoutForm.toggleFormInputs(cashForm, true);
        } else {
            cashForm.style.display = 'none';
            cardForm.style.display = 'block';
            totalAmountSection.style.display = 'none';
            checkoutForm.toggleFormInputs(cashForm, false);
            checkoutForm.toggleFormInputs(cardForm, true);
        }
    },

    toggleFormInputs(form, enabled) {
        form.querySelectorAll('input').forEach(input => {
            input.disabled = !enabled;
            if (enabled && input.name === 'cardNumber') input.required = true;
        });
    },

    updateTotal() {
        let total = 0;
        let totalDisplay = document.getElementById('total');

        document.querySelectorAll('.coin-input').forEach(input => {
            let value = parseInt(input.value) || 0;
            let coinValue = parseInt(input.name.split('[')[1].replace(']', '').replace('.', '').replace(',', ''));
            total += value * coinValue;
        });

        totalDisplay.textContent = (total / 100).toFixed(2);

        if (total >= checkoutForm.targetAmount) {
            Swal.fire({
                title: 'Success!',
                text: "You have reached or exceeded the total amount to pay.",
                icon: 'success'
            });
        }
    },

    handleCashPayment(event) {
        event.preventDefault();
        let total = checkoutForm.calculateTotal();
        if (total < checkoutForm.targetAmount) {
            Swal.fire({
                title: 'Error!',
                text: "You have not reached the total amount to pay.",
                icon: 'error'
            });
        } else {
            document.getElementById("transaction_form_isCashPayment").checked = true;
            $("#transaction_form").submit();
        }
    },

    handleCardPayment(event) {
        event.preventDefault();

        let cardNumber = document.getElementById('transaction_form_cardNumber').value.replace(/\s+/g, '');
        let cardExpiry = document.getElementById('cardExpiry').value;
        let cardCVC = document.getElementById('cardCVC').value;
        if (!checkoutForm.validateCardNumber(cardNumber)) {
            Swal.fire({
                title: 'Error',
                text: 'Invalid card number',
                icon: 'error'
            });
            return;
        }

        if (!checkoutForm.validateExpiry(cardExpiry)) {
            Swal.fire({
                title: 'Error',
                text: 'Invalid expiration date',
                icon: 'error'
            });
            return;
        }

        if (!checkoutForm.validateCVC(cardCVC)) {
            Swal.fire({
                title: 'Error',
                text: 'Invalid CVC',
                icon: 'error'
            });
            return;
        }

        document.getElementById("transaction_form_isCashPayment").checked = false;
        $("#transaction_form").submit();
    },

    formatCardExpiry(event) {
        let input = event.target.value.replace(/\D/g, '');
        if (input.length > 2) {
            input = input.substring(0, 2) + '/' + input.substring(2, 4);
        }
        event.target.value = input;
    },

    validateCardNumber(number) {
        let regex = /^\d{16}$/;
        return regex.test(number);
    },

    validateExpiry(expiry) {
        let regex = /^(0[1-9]|1[0-2])\/?([0-9]{2})$/;
        if (!regex.test(expiry)) return false;

        let [month, year] = expiry.split('/');
        year = '20' + year;

        let expiryDate = new Date(year, month);
        let now = new Date();

        return expiryDate > now;
    },

    validateCVC(cvc) {
        let regex = /^\d{3,4}$/;
        return regex.test(cvc);
    },

    calculateTotal() {
        let total = 0;

        document.querySelectorAll('.coin-input').forEach(input => {
            let value = parseInt(input.value) || 0;
            let coinValue = parseInt(input.name.split('[')[1].replace(']', '').replace('.', '').replace(',', ''));
            total += value * coinValue;
        });

        return total;
    }
};
