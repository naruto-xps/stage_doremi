<?php

namespace App\Console\Commands;

use App\Services\PaymentService;
use Illuminate\Console\Command;

class CheckExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifier et mettre à jour les abonnements expirés';

    /**
     * Execute the console command.
     */
    public function handle(PaymentService $paymentService)
    {
        $this->info('Vérification des abonnements expirés...');

        $result = $paymentService->checkExpiredSubscriptions();

        if ($result['success']) {
            $this->info($result['message']);
            
            if ($result['expired_count'] > 0) {
                $this->warn("Nombre d'abonnements expirés : {$result['expired_count']}");
            } else {
                $this->info('Aucun abonnement expiré trouvé.');
            }
        } else {
            $this->error('Erreur lors de la vérification : ' . $result['message']);
            return 1;
        }

        $this->info('Vérification terminée avec succès !');
        return 0;
    }
}
