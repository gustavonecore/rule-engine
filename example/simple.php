<?php require __DIR__ . '/../vendor/autoload.php';

use GCore\RuleEngine\Contract\StrategyInterface;
use GCore\RuleEngine\Contract\DataSourceInterface;
use GCore\RuleEngine\Composite;
use GCore\RuleEngine\DataSource;
use GCore\RuleEngine\Rule;
use GCore\RuleEngine\RuleEngine;

class AccountCanTransfer extends Composite
{
	/**
	 * @var \DataSource  Local db
	 */
	protected $db;

	/**
	 * Construct the custom specification
	 *
	 * @param mixed   $account
	 * @param integer $amountToTransfer
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfiedBy(DataSourceInterface $payload) : bool
	{
		$accountOrigin = $this->db->get('accounts')[$payload->get('account_origin_id')];

		return ($accountOrigin->balance - $payload->get('amount')) > 0;
	}
}

class PersonIsVerified extends Composite
{
	/**
	 * @var \DataSource  Local db
	 */
	protected $db;

	/**
	 * Construct the custom specification
	 *
	 * @param mixed   $account
	 * @param integer $amountToTransfer
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 * Helper method
	 *
	 * @param dto $person
	 * @return boolean
	 */
	private function isVerified($person) : bool
	{
		return $person->email_verified === 1 && $person->phone_verified === 1 && $person->address_verified === 1;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfiedBy(DataSourceInterface $payload) : bool
	{
		$accountOrigin = $this->db->get('accounts')[$payload->get('account_origin_id')];
		$accountTarget = $this->db->get('accounts')[$payload->get('account_target_id')];

		return $this->isVerified($accountOrigin->person) && $this->isVerified($accountTarget->person);
	}
}

class Taxable extends Composite
{
	/**
	 * @var \array  List of subsidiary that applie tax
	 */
	protected $taxSubidiary;

	/**
	 * Construct the custom specification
	 *
	 * @param integer $taxSubidiary
	 */
	public function __construct(array $taxSubidiary)
	{
		$this->taxSubidiary = $taxSubidiary;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfiedBy(DataSourceInterface $payload) : bool
	{
		return in_array($payload->get('subsidiary'), $this->taxSubidiary);
	}
}

/**
 * Example class to define an strategy to execute if the rule was allowedx
 */
class TransferStrategy implements StrategyInterface
{
	/**
	 * @var \DataSource  Local db
	 */
	protected $db;

	/**
	 * Construct the custom specification
	 *
	 * @param mixed   $account
	 * @param integer $amountToTransfer
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 * {@inheritDoc}
	 */
	public function execute(DataSourceInterface $payload, DataSourceInterface $meta = null)
	{
		$accountOrigin = $this->db->get('accounts')[$payload->get('account_origin_id')];
		$accountTarget = $this->db->get('accounts')[$payload->get('account_target_id')];

		error_log('Start balance origin: ' . $accountOrigin->balance . ', target: ' . $accountTarget->balance);

		$accountTarget->balance += abs($payload->get('amount'));
		$accountOrigin->balance -= abs($payload->get('amount'));

		error_log('Transfering $' . $payload->get('amount') . ', from: ' . $payload->get('account_origin_id') . ' to ' . $payload->get('account_target_id'));
		error_log('New balance origin: ' . $accountOrigin->balance . ', target: ' . $accountTarget->balance);
	}
}

/**
 * Example class to apply tax to every transfered amount
 */
class TaxStrategy implements StrategyInterface
{
	/**
	 * @var \DataSource  Local db
	 */
	protected $db;
	protected $tax;

	/**
	 * Construct the custom specification
	 *
	 * @param mixed   $account
	 * @param integer $amountToTransfer
	 */
	public function __construct($db, float $tax)
	{
		$this->db = $db;
		$this->tax = $tax;
	}

	/**
	 * {@inheritDoc}
	 */
	public function execute(DataSourceInterface $payload, DataSourceInterface $meta = null)
	{
		$accountOrigin = $this->db->get('accounts')[$payload->get('account_origin_id')];
		$accountTarget = $this->db->get('accounts')[$payload->get('account_target_id')];

		$accountOrigin->balance -= abs($payload->get('amount') * $this->tax);
		$accountTarget->balance -= abs($payload->get('amount') * $this->tax);

		error_log('Applying tax of %' . $this->tax . ' for: ' . $payload->get('account_origin_id') . ' and ' . $payload->get('account_target_id'));
		error_log('New balance origin: ' . $accountOrigin->balance . ', target: ' . $accountTarget->balance);
	}
}

$person = new stdClass;
$person->email_verified = 1;
$person->phone_verified = 1;
$person->address_verified = 1;

$personTarget = new stdClass;
$personTarget->email_verified = 1;
$personTarget->phone_verified = 1;
$personTarget->address_verified = 1;

$account = new stdClass;
$account->id = 1;
$account->balance = 1000;
$account->person = $person;

$accountTarget = new stdClass;
$accountTarget->id = 2;
$accountTarget->balance = 0;
$accountTarget->person = $personTarget;

// Just a simple db layer
$db = DataSource::fromArray([
	'accounts' => [
		'1' => $account,
		'2' => $accountTarget,
	],
]);

$transferSpecifications = (new AccountCanTransfer($db))->append(new PersonIsVerified($db));

$rules = [
	new Rule($transferSpecifications, new TransferStrategy($db)),
	new Rule(
		$transferSpecifications->append(new Taxable(['NY', 'AL'])),
		new TaxStrategy($db, 0.2)
	),
	new Rule(
		$transferSpecifications->append(new Taxable(['TX', 'MI'])),
		new TaxStrategy($db, 0.1)
	),
	new Rule(
		$transferSpecifications->append(new Taxable(['FL'])),
		new TaxStrategy($db, 0.4)
	),
];

$transfers = [
	['account_origin_id' => 1, 'amount' => 100, 'account_target_id' => 2, 'subsidiary' => 'NY'],
	['account_origin_id' => 1, 'amount' => 200, 'account_target_id' => 2, 'subsidiary' => 'AL'],
	['account_origin_id' => 1, 'amount' => 300, 'account_target_id' => 2, 'subsidiary' => 'MI'],
	['account_origin_id' => 1, 'amount' => 700, 'account_target_id' => 2, 'subsidiary' => 'FL'],
	['account_origin_id' => 1, 'amount' => 100, 'account_target_id' => 2, 'subsidiary' => 'TX'],
];

$ruleEngine = new RuleEngine($rules);

foreach ($transfers as $transfer)
{
	$datasource = DataSource::fromArray($transfer);

	$ruleEngine->run($datasource);
}