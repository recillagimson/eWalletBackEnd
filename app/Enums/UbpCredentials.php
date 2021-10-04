<?php

namespace App\Enums;

class UbpCredentials
{
    const UserTokenEndpoint = 'https://api-uat.unionbankph.com/partners/sb/customers/v1/oauth2/token';
    const MerchantEndpoint = 'https://api-uat.unionbankph.com/partners/sb/merchants/v4/payments/single';

    const ClientId = 'caa48fdc-d5a5-4353-ad7c-7714af923bbb';
    const ClientSecret = 'xY8vD3xH8lP2dC1eE5rE0fU3lW0vU8sP1yV1jT5tC6hE1rK4sD';
    const PartnerId = '5dff2cdf-ef15-48fb-a87b-375ebff415bb';

    const RedirectUri = 'https://limitless-ocean-99793.herokuapp.com/oauth/redirect';


}
