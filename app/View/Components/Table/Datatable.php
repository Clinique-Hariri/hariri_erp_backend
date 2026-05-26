<?php

namespace App\View\Components\Table;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Datatable extends Component
{
  public $columns;
  public $tableId;
  public $translationPrefix;

  /**
   * Create a new component instance.
   */

  public function __construct($columns, $tableId = 'laravel_datatable', $translationPrefix = 'app')
  {
    $this->columns = $columns;
    $this->tableId = $tableId;
    $this->translationPrefix = $translationPrefix;
  }

  /**
   * Get the view / contents that represent the component.
   */
  public function render(): View|Closure|string
  {
    return view('components.table.datatable');
  }
}
