use App\Models\Commande;
use App\Policies\CommandePolicy;

protected $policies = [
    Commande::class => CommandePolicy::class,
];
