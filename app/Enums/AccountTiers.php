<?php


namespace App\Enums;


class AccountTiers
{
    const tier1 = 'c5d5cb3e-a175-11eb-b447-1c1b0d14e211';
    const tier2 = '5e007ec8-a176-11eb-b447-1c1b0d14e211';

    const silver = '5e007ec8-a176-11eb-b447-1c1b0d14e211';
    const gold = '60d40d2f-a176-11eb-b447-1c1b0d14e211';
    const platinum = '63baa95c-a176-11eb-b447-1c1b0d14e211';
    const diamond = '68d63df8-a176-11eb-b447-1c1b0d14e211';

    const smartPromoTiers = [
        self::silver,
        self::gold,
        self::platinum,
        self::diamond
    ];

}
