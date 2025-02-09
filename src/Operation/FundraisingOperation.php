<?php

namespace MeSomb\Operation;

use DateTime;
use MeSomb\Exception\InvalidClientRequestException;
use MeSomb\Exception\PermissionDeniedException;
use MeSomb\Exception\ServerException;
use MeSomb\Exception\ServiceNotFoundException;
use MeSomb\Model\Contribution;
use MeSomb\Model\ContributionResponse;
use MeSomb\Util\RandomGenerator;
use MeSomb\Util\Util;

class FundraisingOperation extends AOperation
{
    protected $service = 'fundraising';

    /**
     * FundraisingOperation constructor.
     *
     * @param string $applicationKey
     * @param string $accessKey
     * @param string $secretKey
     * @param string $language
     */
    public function __construct($applicationKey, $accessKey, $secretKey, $language = 'en')
    {
        parent::__construct($applicationKey, $accessKey, $secretKey, $language);
    }

    /**
     * Collect money from a mobile account
     *
     * @param array{
     *     amount: float,
     *     service: string,
     *     payer: string,
     *     nonce?: string,
     *     country?: string,
     *     currency?: string,
     *     mode?: string,
     *     conversion?: bool,
     *     anonymous?: bool,
     *     accept_terms?: bool,
     *     location?: array{
     *         town: string,
     *         region: string,
     *         location: string
     *     },
     *     contact?: array{
     *         phone_number: string,
     *         email?: string,
     *     },
     *     full_name?: array{
     *         first_name: string,
     *         last_name?: string,
     *     },
     *     trxID?: string,
     * } $params
     *
     * @return ContributionResponse
     * @throws InvalidClientRequestException
     * @throws ServerException
     * @throws ServiceNotFoundException
     * @throws PermissionDeniedException
     */
    public function makeContribution(array $params) {
        $endpoint = 'fundraising/contribute/';

        if (Util::getOrDefault($params, 'anonymous', false) !== true) {
            assert(Util::getOrDefault($params, 'contact') != null);
            assert(Util::getOrDefault($params, 'full_name') != null);
        }
        assert($params['amount'] > 0);

        $body = [
            'amount' => $params['amount'],
            'service' => $params['service'],
            'payer' => $params['payer'],
            'country' => Util::getOrDefault($params, 'country', 'CM'),
            'amount_currency' => Util::getOrDefault($params, 'currency', 'XAF'),
            'anonymous' => Util::getOrDefault($params, 'anonymous', false),
            'accept_terms' => Util::getOrDefault($params, 'accept_terms', true),
            'conversion' => Util::getOrDefault($params, 'conversion', false),
        ];
        if (!is_null(Util::getOrDefault($params, 'trxID'))) {
            $body['trxID'] = $params['trxID'];
        }
        if (!is_null(Util::getOrDefault($params, 'location'))) {
            $body['location'] = $params['location'];
        }
        if (!is_null(Util::getOrDefault($params, 'contact'))) {
            $body['contact'] = [
                'phone_number' => $params['contact']['phone_number'],
            ];
            if (!is_null(Util::getOrDefault($params['contact'], 'email'))) {
                $body['contact']['email'] = $params['contact']['email'];
            }
        }
        if (!is_null(Util::getOrDefault($params, 'full_name'))) {
            $body['full_name'] = [
                'last_name' => $params['full_name']['last_name'],
            ];
            if (!is_null(Util::getOrDefault($params['full_name'], 'first_name'))) {
                $body['full_name']['first_name'] = $params['full_name']['first_name'];
            }
        }

        return new ContributionResponse($this->executeRequest('POST', $endpoint, new DateTime(), Util::getOrDefault($params, 'nonce', RandomGenerator::nonce()), $body, Util::getOrDefault($params, 'mode', 'synchronous')));
    }

    /**
     * Get contributions stored in MeSomb based on the list
     *
     * @param array $ids list of ids
     * @return Contribution[]|void
     * @throws InvalidClientRequestException
     * @throws PermissionDeniedException
     * @throws ServerException
     * @throws ServiceNotFoundException
     */
    public function getContributions(array $ids, $source = 'MESOMB')
    {
        assert(count($ids) > 0);
        assert($source == 'MESOMB' || $source == 'EXTERNAL');

        $endpoint = "fundraising/contributions/?ids=".implode(',', $ids)."&source=".$source;

        return array_map(function ($v) {
            return new Contribution($v);
        }, $this->executeRequest('GET', $endpoint, new DateTime(), ''));
    }

    /**
     * Reprocess contribution at the operators level to confirm the status of a contribution
     *
     * @param array $ids list of ids
     *
     * @return Contribution[]|void
     * @throws InvalidClientRequestException
     * @throws PermissionDeniedException
     * @throws ServerException
     * @throws ServiceNotFoundException
     */
    public function checkContributions(array $ids, $source = 'MESOMB')
    {
        assert(count($ids) > 0);
        assert($source == 'MESOMB' || $source == 'EXTERNAL');

        $endpoint = "fundraising/contributions/check/?ids=".implode(',', $ids)."&source=".$source;

        return array_map(function ($v) {
            return new Contribution($v);
        }, $this->executeRequest('GET', $endpoint, new DateTime(), ''));
    }
}