<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "mfa_yubikey" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Derhansen\MfaYubikey\Command;

use Derhansen\MfaYubikey\Service\YubikeyAuthService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class CheckYubiKeyOtpCommand
 */
class CheckYubiKeyOtpCommand extends Command
{
    public function configure()
    {
        $this
            ->setDescription('Checks the given OTP against the configured YubiKey endpoints')
            ->addArgument(
                'otp',
                InputArgument::REQUIRED,
                'The YubiKey OTP'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());

        $yubikeyAuth = GeneralUtility::makeInstance(YubikeyAuthService::class);
        $otp = $input->getArgument('otp');

        if ($yubikeyAuth->verifyOtp($otp)) {
            $io->success('OK: ' . $otp . ' has been successfully validated.');
            return 0;
        }
        $io->error($otp . '  could not be validated. Reasons: ' . implode(' / ', $yubikeyAuth->getErrors()));
        return 1;
    }
}
