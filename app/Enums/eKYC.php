<?php


namespace App\Enums;


class eKYC
{
    const eKYC = "1a08a7af-6188-4b6f-98b8-984c97c5ee53";
    const eKYC_remarks = "eKYC Approval";

    const returnableFields = [
        'surname', 'name', 'middlename', 'id', 'doe', 'valid_date', 'lic_no', 'dob'
    ];

    const lastNameKey = [
        'last_name', 'surname',
    ];

    const idNumberKey = [
        'lic_no', 'id', 'passport_num'
    ];

    const fullNameKey = ['name'];
    const firstNameKey = ['name', 'firstname', 'first_name'];
    const middleNameKey = ['middlename', 'middle_name'];
    const expirationDateKey = ['doe', 'valid_date'];
    const dateOfBirth = ['dob'];

    const Passport = '0edb764a-9131-11eb-b44f-1c1b0d14e211';
    const PRC = '0edb7c7e-9131-11eb-b44f-1c1b0d14e211';
    const DL = '0edb7b31-9131-11eb-b44f-1c1b0d14e211';
}
