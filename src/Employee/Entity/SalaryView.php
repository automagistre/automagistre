<?php

declare(strict_types=1);

namespace App\Employee\Entity;

use App\CreatedBy\Entity\CreatedByView;
use App\Customer\Entity\OperandId;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="salary_view")
 */
class SalaryView
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="salary_id")
     */
    public SalaryId $id;

    /**
     * @ORM\Column(type="employee_id")
     */
    public EmployeeId $employeeId;

    /**
     * @ORM\Column(type="operand_id")
     */
    public OperandId $personId;

    /**
     * @ORM\Column(type="integer")
     */
    public int $payday;

    /**
     * @ORM\Column(type="money")
     */
    public Money $amount;

    /**
     * @ORM\Column(type="created_by_view")
     */
    public CreatedByView $created;

    /**
     * @ORM\Column(type="created_by_view")
     */
    public ?CreatedByView $ended = null;

    private function __construct(
        SalaryId $id,
        EmployeeId $employeeId,
        OperandId $personId,
        int $payday,
        Money $amount,
        CreatedByView $created,
        ?CreatedByView $ended
    ) {
        $this->id = $id;
        $this->employeeId = $employeeId;
        $this->personId = $personId;
        $this->payday = $payday;
        $this->amount = $amount;
        $this->created = $created;
        $this->ended = $ended;
    }

    public static function sql(): string
    {
        return '
            CREATE VIEW salary_view AS
            SELECT es.id,
                es.employee_id,
                es.payday,
                es.amount,
                employee.person_id AS person_id,
                CONCAT_WS(
                    \';\',
                    CONCAT_WS(
                        \',\',
                        es_cb_u.uuid,
                        es_cb_u.username,
                        COALESCE(es_cb_u.last_name, \'\'),
                        COALESCE(es_cb_u.first_name, \'\')
                    ),
                    es_cb.created_at
                ) AS created,
                CONCAT_WS(
                    \';\',
                    CONCAT_WS(
                        \',\',
                        ese_cb_u.uuid,
                        ese_cb_u.username,
                        COALESCE(ese_cb_u.last_name, \'\'), 
                        COALESCE(ese_cb_u.first_name, \'\')
                    ),
                    ese_cb.created_at
                ) AS ended
            FROM employee_salary es
                JOIN created_by es_cb ON es_cb.id = es.id
                JOIN users es_cb_u ON es_cb_u.uuid = es_cb.user_id
                JOIN employee ON employee.uuid = es.employee_id
                LEFT JOIN employee_salary_end ese ON es.id = ese.salary_id
                LEFT JOIN created_by ese_cb ON ese_cb.id = ese.id
                LEFT JOIN users ese_cb_u ON ese_cb_u.uuid = ese_cb.user_id
        ';
    }
}
