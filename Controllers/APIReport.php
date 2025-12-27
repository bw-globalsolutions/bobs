<?php
require_once("Models/StatisticsModel.php");

class APIReport extends Controllers{

	public function __construct()
    {
        parent::__construct();
    }

    public function genReport(){
        $objStatistics = new StatisticsModel();
        $arrAudits = $objStatistics->getAuditDay(1);
        $file = 'Assets/audit_report_csv/audit_report_' . date("w") . '.csv';

        $fp = fopen($file, 'w');

        if(!empty($arrAudits)){
            fputcsv($fp, array_keys($arrAudits[0]));
            foreach($arrAudits as $audit){
                fputcsv($fp, array_values($audit));
            }
        }
        fclose($fp);
        die('success');
    }

    public function putReport(){

        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        ini_set("max_execution_time", "-1");
        ini_set("memory_limit", "-1");
        ignore_user_abort(true);
        set_time_limit(0);

        include('Libraries/phpseclib/Net/SFTP.php');

        $sftp = new Net_SFTP('drop.smallssliders.com');
        if (!$sftp->login('Arguilea', 'L3monP3pper')) {
            exit('Login Failed');
        }
        $remote_file = 'Audit/arguilea_audits_'. date('y_m_d') . '.csv';
        $local_file = 'Assets/audit_report_csv/audit_report_' . date("w") . '.csv';
        $sftp->put($remote_file, $local_file, NET_SFTP_LOCAL_FILE); 

        $sftp->disconnect();
        die('success');
        
    }
}
?>