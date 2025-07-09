<?php

// Import additionnal class into the global namespace
use LaswitchTech\Core\Abstracts\Command;

class OllamaCommand extends Command {

    /**
     * Constructor
     */
    public function __construct()
    {
        // Call Parent Constructor
        parent::__construct();
    }

    public function testAction()
    {
        $prompt = 'Here is the email:\n\nFrom pars@albcustoms.com\nTo release@albcustoms.com; release@albcie.com\nDate 2024-05-30 2:10:37 PM\nSubject [TR:10013001148517] [CCN:4069EXLA1388215724] [CROSSING:0440] [LOCATION:] [CN: [REF_NUM_CONT:CN# 4069EXLA1388215724]] [PO:027346] [INV:53249] [REF_NUM:CCN# 4069EXLA1388215724] [PKG:1] [WEIGHT:92.08KGM] [VENDOR:DUCKY-UMN] [CLIENT:LEISP-3559] [SBRN:139467674RM0001]\n\nBonjour/Hi\nVotre expedition a ete relache et pret a etre ramasse. Voici quelque details:\nYour shipment has been released and is ready for pick-up. Here are some details:\n \nDetails\n================================================\nClient Code : LEISP-3559\n \nConsignee : LEIS PET DISTRIBUTING INCATLANTIC PET\nAddress : 1315 HUTCHISON ROAD\nCity : WELLESLEY\nPostal Code : N0B2T0\nProvince : ON\nCountry : CA\n\nTransaction Number : 10013001148517\nCargo Control Number : 4069EXLA1388215724\nContainer Number(s) :\nPO(s) : 027346\nINV(s) : 53249\nNumber of Packages : 1\nWeight : 92.08 KGM\n \nVeuillez contacter pars@albcustoms.com pour toute requetes.\nPlease contact pars@albcustoms.com for any inqueries.\n \nCordialement/Regards\nALB Team';
        $this->Helper->Ollama->prompt($prompt);
        var_dump($this->Helper->Ollama->request());
    }
}
