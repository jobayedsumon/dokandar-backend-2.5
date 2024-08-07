<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'f_name',
        'l_name',
        'phone',
        'email',
        'password',
        'login_medium',
        'ref_by',
        'social_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'interest',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_phone_verified' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'order_count' => 'integer',
        'wallet_balance' => 'float',
        'loyalty_point' => 'integer',
        'ref_by' => 'integer',
    ];


    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function addresses(){
        return $this->hasMany(CustomerAddress::class);
    }

    public function userinfo()
    {
        return $this->hasOne(UserInfo::class,'user_id', 'id');
    }

    public function scopeZone($query, $zone_id=null){
        $query->when(is_numeric($zone_id), function ($q) use ($zone_id) {
            return $q->where('zone_id', $zone_id);
        });
    }

    public function customer_investments()
    {
        return $this->hasMany(CustomerInvestment::class, 'customer_id');
    }

    public function investment_withdrawals()
    {
        return $this->hasMany(InvestmentWithdrawal::class, 'customer_id');
    }

    public function wallet_transactions()
    {
        return $this->hasMany(WalletTransaction::class, 'user_id');
    }

    public function investment_payments()
    {
        return $this->hasMany(InvestmentPayment::class, 'customer_id');
    }

    public function total_investment_withdrawals()
    {
        return ($this->investment_withdrawals()->sum('withdrawal_amount') + $this->investment_withdrawals()->sum('withdrawal_charge'));
    }

    public function total_redeemed_investments()
    {
        return $this->customer_investments()
            ->where('redeemed_at', '!=', null)
            ->withSum('package', 'amount')
            ->get()
            ->sum('package_sum_amount');
    }

    public function total_investments_profit()
    {
        return $this->customer_investments()
            ->get()
            ->sum('profit_earned');
    }

    public function total_investment_to_wallet_transfer()
    {
        return $this->wallet_transactions()
            ->where('transaction_type', 'investment_to_wallet')
            ->sum('credit');
    }

    public function total_investment_payment()
    {
        return $this->investment_payments()
            ->where('payment_method', 'investment_balance')
            ->where('payment_status', 'success')
            ->sum('amount');
    }

    public function getInvestmentWalletAttribute(): object
    {
        $profit             = $this->total_investments_profit();
        $redeemed           = $this->total_redeemed_investments();
        $withdrawal         = $this->total_investment_withdrawals();
        $transfer           = $this->total_investment_to_wallet_transfer();
        $investment_payment = $this->total_investment_payment();

        return (object) [
            'profit'             => $profit,
            'redeemed'           => $redeemed,
            'withdrawal'         => $withdrawal,
            'transfer'           => floatval($transfer),
            'investment_payment' => floatval($investment_payment),
            'balance'            => $profit + $redeemed - $withdrawal - $transfer - $investment_payment,
        ];
    }
}
