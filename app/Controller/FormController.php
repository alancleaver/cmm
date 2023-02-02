<?php

namespace App\Controller;

use App\Service\CompanyMatcher;
use App\Model\Companies;

class FormController extends Controller
{
    public function index()
    {
        $this->render('form.twig');
    }

    public function submit($params)
    {
        //We just need the front non-numeric part of the postcode.
        $partial_outward_postcode = str_replace(range(0, 9), null, substr($params['postcode'] ?: '', 0, 2));

        $companies = new Companies($this->db());

        $matchedCompanies = $companies->randomize()
                                ->limit($_ENV['MAX_MATCHED_COMPANIES'])
                                ->filterInCreditOnly()
                                ->filterByPostcode([$partial_outward_postcode])
                                ->filterByBedrooms([$params['bedrooms']])
                                ->filterByTypes([$params['type']]);

        $this->render('results.twig', [
            'matchedCompanies'  => $matchedCompanies,
        ]);

        //We've output these companies to the browser, now we should decrement a credit
        foreach ($matchedCompanies as $company) {
            $company->decrementCredits();
        }
    }
}