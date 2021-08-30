import {loadStripe} from '@stripe/stripe-js';

// https://stripe.com/docs/billing/subscriptions/elements
const stripe = await loadStripe(`${process.env.stripePublicKey}`);

let elements = stripe.elements({'locale':'auto'});
let cardElementCardNumber = elements.create('card', { 'hidePostalCode': true });
cardElementCardNumber.mount('#card-element-cardnumber');

const submitButton = document.querySelector('#submit-payment-button');
submitButton.setAttribute('disabled', 'disabled');

let formValid = { 
  'priceId': false,
  'cardName': false,
  'cardNumber': false,
};
let formValues = {
  'priceId': '',
  'cardName': '',
  'token': '',
};

const priceIds = document.querySelectorAll('[name="price-id"]');
const cardMessage = document.querySelector('#card-message');
const cardName = document.querySelector('#card-name');
const cardNameMessage = document.querySelector('#card-name-message');

function checkCard(){
    let check = (formValid.cardNumber == true && formValid.cardName == true && formValid.priceId == true) ? true : false;
    if(check){
        submitButton.removeAttribute('disabled');
    } else {
        submitButton.setAttribute('disabled', 'disabled');
    }
    return check;
}

cardElementCardNumber.on('change', (event) => {
    
    if (event.error) {
        cardMessage.textContent = event.error.message;
        formValid.cardNumber = false;
        cardMessage.classList.remove('d-none');
    } else {
        cardMessage.textContent = '';
        cardMessage.classList.add('d-none');
    }
    if (event.complete) {
        formValid.cardNumber = true;
    }
    checkCard();
});

cardName.addEventListener('change', (event) => {
    if( cardName.value ){
        formValues.cardName = cardName.value;
        formValid.cardName = true;
        cardNameMessage.classList.add('d-none');
    } else {
        cardNameMessage.textContent = '名前の入力は必須です。';
        cardNameMessage.classList.remove('d-none');
        formValid.cardName = false;
    }
    checkCard();
});


priceIds.forEach((priceId) => {
    priceId.addEventListener('change', (event) => {
        if(priceId.value){
            formValues.priceId = priceId.value;
            formValid.priceId = true;
        }
        checkCard();
    });
});

submitButton.addEventListener('click', (e) => {
    
    e.preventDefault();
    document.querySelector('#text-subscribe-spinner').classList.remove('d-none');
    document.querySelector('#text-subscribe').classList.add('d-none');
    submitButton.setAttribute('disabled', 'disabled');
    cardName.setAttribute('disabled', 'disabled');
    cardElementCardNumber.update({ disabled: true });
    priceIds.forEach((priceId) => {
        priceId.setAttribute('disabled', 'disabled');
    });
    
    stripe.createToken(cardElementCardNumber, {'name':cardName.value}).then((result)=>{
        
        if(result.error){
            cardNameMessage.textContent = 'このカードは利用できません';
            cardNameMessage.classList.remove('d-none');
            formValid.cardName = false;
        }
        else if(result.token){
            
            formValues.token = result.token;
            fetch('/payment/_____stripe_____createSubscription/', {
                method: 'POST',
                headers: {},
                body: JSON.stringify(formValues)
            }).then(function(response){
                if(response.status == 200){
                    return response.json();
                } else {
                    location.href = '/error/500?code=createSubscriptionError';
                }
            }).then(function(result_json){
                if(result_json.success == false){
                    location.href = '/error/500?code=clientSecretError';                    
                }
                const clientSecret = result_json.client_secret;
                stripe.confirmCardPayment(clientSecret, {
                    payment_method: {
                        card: cardElementCardNumber,
                        billing_details: {
                            name: formValues.cardName,
                        },
                    }
                }).then((result) => {
                    if(result.error) {
                        location.href = '/error/500?code=confirmCardPaymentError';
                    } else {
                        location.href = result_json.redirect_url;
                    }
                });
            });
        }

    });

});
