<?php

namespace Railken\LaraOre\EmailSender;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Railken\LaraOre\DataBuilder\DataBuilder;
use Railken\Laravel\Manager\Contracts\EntityContract;

/**
 * @property string      $name
 * @property string      $description
 * @property DataBuilder $data_builder
 * @property string      $body
 * @property string      $attachments
 * @property string      $subject
 * @property string      $sender
 * @property string      $recipients
 */
class EmailSender extends Model implements EntityContract
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'email_sender';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'sender',
        'recipients',
        'subject',
        'body',
        'attachments',
        'description',
        'data_builder_id',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Creates a new instance of the model.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = Config::get('ore.email-sender.table');
        $this->fillable = array_merge($this->fillable, array_keys(Config::get('ore.email-sender.attributes')));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function data_builder()
    {
        return $this->belongsTo(DataBuilder::class);
    }
}
