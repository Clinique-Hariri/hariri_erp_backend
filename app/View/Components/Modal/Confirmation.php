<?php

namespace App\View\Components\Modal;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Confirmation extends Component
{
  public $id, $title, $action, $inputs, $confirmationTitle, $confirmationText, $checkboxLabel;
  public $requireConfirmation, $method, $submitLabel, $cancelLabel, $submitClass, $size, $theme;

  /**
   * Create a new component instance.
   */
  public function __construct(
    $id,
    $title,
    $action,
    $inputs = '',
    $confirmationTitle = null,
    $confirmationText = null,
    $checkboxLabel = null,
    $requireConfirmation = true,
    $method = 'POST',
    $submitLabel = null,
    $cancelLabel = null,
    $theme = 'primary',
    $size = 'md'
  )
  {
    $this->id = $id;
    $this->title = $title ?? __('app.modal.confirm.title');
    $this->action = $action;
    $this->inputs = $inputs;
    $this->confirmationTitle = $confirmationTitle ?? __('app.modal.confirm.question');
    $this->confirmationText = $confirmationText ?? __('app.modal.confirm.text');
    $this->checkboxLabel = $checkboxLabel ?? __('app.modal.confirm.checkbox');
    $this->requireConfirmation = $requireConfirmation;
    $this->method = $method;
    $this->submitLabel = $submitLabel ?? __('app.submit');
    $this->cancelLabel = $cancelLabel ?? __('app.cancel');
    $this->theme = $theme;
    $this->size = $size;
  }

  /**
   * Get the view / contents that represent the component.
   */
  public function render(): View|Closure|string
  {
    return view('components.modal.confirmation');
  }
}
