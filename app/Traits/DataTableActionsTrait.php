<?php

namespace App\Traits;

trait DataTableActionsTrait
{
  private bool $showBtn = false;
  private string $showRoute;
  private string $showColor = 'info';

  private ?string $html = "";

  private bool $deleteBtn = false;
  private bool $deleteActionInModel = false;
  private string $deleteRoute;
  private string $deleteColor = 'danger';

  private bool $editBtn = false;
  private string $editRoute;
  private string $editColor = 'primary';

  private bool $importBtn = false;
  private string $importRoute;

  private bool $status = false;

  private array $redirectButtons = [];
  protected array $modalButtons = [];
  protected array $actionButtons = [];

  public function edit(string $route, bool $show = true, string $color = 'primary'): static
  {
    $this->editBtn = $show;
    $this->editRoute = $route;
    $this->editColor = $color;
    return $this;
  }

  public function show(string $route, bool $show = true, string $color = 'info'): static
  {
    $this->showBtn = $show;
    $this->showRoute = $route;
    $this->showColor = $color;
    return $this;
  }

  public function import(string $route, bool $show = true): static
  {
    $this->importBtn = $show;
    $this->importRoute = $route;
    return $this;
  }

  public function redirectButton($route, $text, $icon = "bx bxs-link-external", $show = true, $target = "_self", string $color = 'dark'): static
  {
    if (!$show) {
      return $this;
    }

    $this->redirectButtons[] = [
      'route' => $route,
      'text' => $text,
      'icon' => $icon,
      'target' => $target,
      'color' => $color
    ];

    return $this;
  }

  public function actionButton(string $class, string $text, string $data_id = "", string $icon = "bx bx-dots-vertical-rounded", bool $show = true, string $color = 'dark'): static
  {
    if (!$show) {
      return $this;
    }

    $this->actionButtons[] = [
      'class' => $class,
      'text' => $text,
      'data_id' => $data_id,
      'icon' => $icon,
      'color' => $color,
    ];

    return $this;
  }

  public function modalButton(string $modalId, string $text, string $icon = "bx bx-window-open", array $data = [], bool $show = true, string $color = 'dark'): static
  {
    if (!$show) {
      return $this;
    }

    $this->modalButtons[] = [
      'modalId' => $modalId,
      'text' => $text,
      'icon' => $icon,
      'data' => $data,
      'color' => $color
    ];

    return $this;
  }

  public function button($html = ""): static
  {
    $this->html = $html;
    return $this;
  }

  public function delete($table_id, bool $show = true, bool $actionInModel = true, string $color = 'danger'): static
  {
    $this->deleteBtn = $show;
    $this->deleteActionInModel = $actionInModel;
    $this->deleteRoute = $table_id;
    $this->deleteColor = $color;

    return $this;
  }

  public function make(): string
  {
    $html = '<div class="dropdown">
              <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                <i class="bx bx-dots-vertical-rounded"></i>
              </button>
              <div class="dropdown-menu">';

    if ($this->showBtn) {
      $html .= '<a href="' . $this->showRoute . '" class="dropdown-item show" table_id="' . $this->showRoute . '">
                    <i class="bx bxs-show me-2"></i> ' . __('app.show') . '
                  </a>';
    }

    if ($this->editBtn) {
      $html .= '<a href="' . $this->editRoute . '" class="dropdown-item edit" table_id="' . $this->editRoute . '">
                    <i class="bx bxs-edit me-2"></i> ' . __('app.edit') . '
                  </a>';
    }

    // Render redirect buttons
    foreach ($this->redirectButtons as $btn) {
      $html .= '<a href="' . $btn['route'] . '" class="dropdown-item" target="' . $btn['target'] . '">
                    <i class="' . $btn['icon'] . ' me-2"></i> ' . $btn['text'] . '
                  </a>';
    }

    // Render modal buttons
    foreach ($this->modalButtons as $btn) {
      $dataAttrs = '';
      foreach ($btn['data'] as $key => $val) {
        $dataAttrs .= ' data-' . $key . '="' . $val . '"';
      }

      $html .= '<a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#' . $btn['modalId'] . '"' . $dataAttrs . '>
                    <i class="' . $btn['icon'] . ' me-2"></i> ' . $btn['text'] . '
                  </a>';
    }

    // Custom HTML if any
    $html .= $this->html;

    // Delete button
    if ($this->deleteBtn) {
      $html .= '<a class="dropdown-item delete" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete-modal" data-id="' . $this->deleteRoute . '">
                    <i class="text-danger bx bxs-trash me-2"></i>
                    <span class="text-danger">' . __("app.delete") . '</span>
                  </a>';
    }

    $html .= '</div></div>';

    // Reset all button states
    $this->resetButtonStates();

    return $html;
  }

  public function makeIconsOnly(): string
  {
    $html = '<div class="d-flex align-items-sm-center justify-content-sm-center">';

    // Show button
    if ($this->showBtn) {
      $html .= '<a href="' . $this->showRoute . '" class="btn btn-sm btn-icon me-2 show" title="' . __('app.show') . '">
                    <i class="bx bxs-show"></i>
                  </a>';
    }

    // Edit button
    if ($this->editBtn) {
      $html .= '<a href="' . $this->editRoute . '" class="btn btn-sm btn-icon me-2 edit" title="' . __('app.edit') . '">
                    <i class="bx bxs-edit"></i>
                  </a>';
    }

    // Redirect buttons
    foreach ($this->redirectButtons as $btn) {
      $html .= '<a href="' . $btn['route'] . '" class="btn btn-sm btn-icon me-2" target="' . $btn['target'] . '" title="' . $btn['text'] . '">
                    <i class="' . $btn['icon'] . '"></i>
                  </a>';
    }

    // Modal buttons
    foreach ($this->modalButtons as $btn) {
      $dataAttrs = '';
      foreach ($btn['data'] as $key => $val) {
        $dataAttrs .= ' data-' . $key . '="' . $val . '"';
      }

      $html .= '<button type="button" class="btn btn-sm btn-icon me-2" data-bs-toggle="modal" data-bs-target="#' . $btn['modalId'] . '"' . $dataAttrs . ' title="' . $btn['text'] . '">
                    <i class="' . $btn['icon'] . '"></i>
                  </button>';
    }

    // Action buttons
    foreach ($this->actionButtons as $btn) {
      $html .= '<button type="button" class="btn btn-sm btn-icon me-2 ' . $btn['class'] . '" data-id="' . $btn['data_id'] . '" title="' . $btn['text'] . '">
                    <i class="' . $btn['icon'] . '"></i>
                  </button>';
    }

    // Delete button
    if ($this->deleteBtn) {
      $html .= '<button type="button" class="btn btn-sm btn-icon text-danger delete" data-bs-toggle="modal" data-bs-target="#delete-modal" data-id="' . $this->deleteRoute . '" title="' . __('app.delete') . '">
                    <i class="bx bx-trash"></i>
                  </button>';
    }

    $html .= '</div>';

    // Reset all button states
    $this->resetButtonStates();

    return $html;
  }

  public function makeLabelledIcons(): string
  {
    $html = '<div class="d-flex align-items-center">';

    // Show
    if ($this->showBtn) {
      $html .= '<a href="' . $this->showRoute . '" class="btn btn-icon btn-label-' . $this->showColor . ' inline-spacing view" title="' . __('app.show') . '">
                    <span class="tf-icons bx bxs-show"></span>
                  </a>';
    }

    // Edit
    if ($this->editBtn) {
      $html .= '<a href="' . $this->editRoute . '" class="btn btn-icon btn-label-' . $this->editColor . ' inline-spacing edit" title="' . __('app.edit') . '">
                    <span class="tf-icons bx bxs-edit"></span>
                  </a>';
    }

    // Redirects
    foreach ($this->redirectButtons as $btn) {
      $html .= '<a href="' . $btn['route'] . '" target="' . $btn['target'] . '" class="btn btn-icon btn-label-' . $btn['color'] . ' inline-spacing" title="' . $btn['text'] . '">
                    <span class="tf-icons ' . $btn['icon'] . '"></span>
                  </a>';
    }

    // Modals
    foreach ($this->modalButtons as $btn) {
      $dataAttrs = '';
      foreach ($btn['data'] as $k => $v) {
        $dataAttrs .= ' data-' . $k . '="' . $v . '"';
      }

      $html .= '<button type="button" class="btn btn-icon btn-label-' . $btn['color'] . ' inline-spacing" data-bs-toggle="modal" data-bs-target="#' . $btn['modalId'] . '" title="' . $btn['text'] . '"' . $dataAttrs . '>
                    <span class="tf-icons ' . $btn['icon'] . '"></span>
                  </button>';
    }

    // Actions
    foreach ($this->actionButtons as $btn) {
      $html .= '<button type="button" class="btn btn-icon btn-label-' . $btn['color'] . ' inline-spacing ' . $btn['class'] . '" data-id="' . $btn['data_id'] . '" title="' . $btn['text'] . '">
                    <span class="tf-icons ' . $btn['icon'] . '"></span>
                  </button>';
    }

    // Delete
    if ($this->deleteBtn) {
      $html .= '<button type="button" class="btn btn-icon btn-label-' . $this->deleteColor . ' inline-spacing delete" data-bs-toggle="modal" data-bs-target="#delete-modal" data-id="' . $this->deleteRoute . '" title="' . __('app.delete') . '">
                    <span class="tf-icons bx bx-trash"></span>
                  </button>';
    }

    $html .= '</div>';

    // Reset all button states
    $this->resetButtonStates();

    return $html;
  }

  private function resetButtonStates(): void
  {
    $this->showBtn = false;
    $this->showColor = 'info';
    $this->editBtn = false;
    $this->editColor = 'primary';
    $this->deleteBtn = false;
    $this->deleteColor = 'danger';
    $this->redirectButtons = [];
    $this->actionButtons = [];
    $this->modalButtons = [];
    $this->html = "";
  }

  public static function GroupActions(array $groups): string
  {
    $html  = '<div class="d-flex align-items-center">';
    foreach ($groups as $group) {
      $html .= $group;
    }
    $html .= '</div>';
    return $html;
  }

  public static function image($imageUrl, $title = "image"): string
  {
    $html = "<ul class='list-unstyled users-list m-0 avatar-group d-flex align-items-center'>";
    $html .= "<li data-bs-toggle='tooltip' data-popup='tooltip-custom' data-bs-placement='top' class='avatar avatar-md pull-up' title='$title'>";
    $html .= "<img src='$imageUrl' alt='Avatar' class='rounded-circle'>";
    $html .= "</li>";
    $html .= "</ul>";
    return $html;
  }

  public static function imageRectangle($imageUrl, $title = "image"): string
  {
    $html = "<ul class='list-unstyled users-list m-0 avatar-group d-flex align-items-center'>";
    $html .= "<li data-bs-toggle='tooltip' data-popup='tooltip-custom' data-bs-placement='top' class='avatar avatar me-2 rounded-2 bg-label-secondary avatar-md pull-up' title='$title'>";
    $html .= "<img src='$imageUrl' alt='Avatar' class='rounded-2'>";
    $html .= "</li>";
    $html .= "</ul>";
    return $html;
  }

  public static function images(array $images): string
  {
    $html = "<ul class='list-unstyled users-list m-0 avatar-group d-flex align-items-center'>";
    foreach ($images as $image) {
      $html .= "<li data-bs-toggle='tooltip' data-popup='tooltip-custom' data-bs-placement='top' class='avatar avatar-xs pull-up' title='Lilian Fuller'>";
      $html .= "<img src='$image' alt='Avatar' class='rounded-circle'>";
      $html .= "</li>";
    }
    $html .= "</ul>";
    return $html;
  }

  public function color($colorCode): string
  {
    return "<div style='width: 30px;height:30px;/*margin: auto;*/border-radius: 50%;background-color: " . $colorCode . "'></div>";
  }

  public static function share($links, $name): string
  {
    return '<a href="' . url('https://api.whatsapp.com/send?text=' . urlencode($links)) . '" target="__blank">' . $name . ' <span class="menu-icon ml-5"><i class="bi bi-share fs-3"></i></span></a> ' .
      '<span class="copy-link" onclick="copyToClipboard(\'' . $links . '\')"><i class="bi bi-files fs-3"></i></span>';
  }

  public static function tags($tags): string
  {
    $tagHtml = [];
    foreach ($tags as $tag) {
      $tagHtml[] = '<span class="badge badge-primary">' . e($tag) . '</span>';
    }
    ;
    return implode(' ', $tagHtml);
  }

  public static function money($amount): string
  {
    $formattedAmount = number_format($amount, 2, '.', ' ');
    $currency = __('app.DZD');
    if (app()->getLocale() == 'ar') {
      return '<span class="badge bg-label-primary me-1" dir="ltr">' . $currency . ' &lrm;' . $formattedAmount . '</span>';
    } else {
      return '<span class="badge bg-label-primary me-1">' . $formattedAmount . ' ' . $currency . '</span>';
    }
  }

  public static function percentage($amount): string
  {
    if (app()->getLocale() == 'ar') {
      return '<span class="badge bg-label-primary me-1" dir="ltr">% ' . $amount . '</span>';
    }
    return '<span class="badge bg-label-primary me-1">' . $amount . ' %</span>';
  }

  public static function bold($text): string
  {
    return "<strong>$text</strong>";
  }

  public static function badge($code, $text, $icon = null): string
  {
    if (empty($text)) {
      return '-';
    }
    $html = '<span class="badge bg-label-' . $code . ' me-1">';

    if ($icon) {
      $html .= '<span class="' . $icon . '"></span>';
    }
    $html .= $text . '</span>';

    return $html;
  }

  public static function boolean($status): string
  {
    return $status ? '<span class="badge bg-label-success me-1">' . __('app.yes') . '</span>' : '<span class="badge bg-label-danger me-1">' . __('app.no') . '</span>';
  }

  public static function date($date): string
  {
    return $date ? $date->format('Y-m-d') : '';
  }

  public static function time($time): string
  {
    return $time ? $time->format('H:i') : '';
  }

  public static function link($url, $maxLength = 30, $displayUrl = null): string
  {
    if (empty($url)) {
      return '-';
    }
    $text = $displayUrl ?? $url;
    if (app()->getLocale() == 'ar') {
      $text = strlen($text) > $maxLength ? '...' . substr($text, 0, $maxLength) : $text;
    } else {
      $text = strlen($text) > $maxLength ? substr($text, 0, $maxLength) . '...' : $text;
    }
    return '<a href="' . $url . '" target="_blank" title="' . htmlspecialchars($url) . '">' . htmlspecialchars($text) . '</a>';
  }

  public static function longText($text, $maxLength = 30): string
  {
    if (empty($text)) {
      return '-';
    }
    if (app()->getLocale() == 'ar') {
      $displayText = mb_strlen($text, 'UTF-8') > $maxLength
        ? '...' . mb_substr($text, 0, $maxLength, 'UTF-8')
        : $text;
    } else {
      $displayText = mb_strlen($text, 'UTF-8') > $maxLength
        ? mb_substr($text, 0, $maxLength, 'UTF-8') . '...'
        : $text;
    }
    return $displayText;
  }

  public static function rating(int $rating): string
  {
    $output = '';
    for ($i = 1; $i <= 5; $i++) {
      if ($i <= $rating) {
        $output .= '<span style="color: gold;">&#9733;</span>'; // filled star
      } else {
        $output .= '<span style="color: lightgray;">&#9733;</span>'; // empty star
      }
    }
    return $output;
  }

  public static function toHtml($text): string
  {
    return htmlspecialchars_decode($text);
  }

  public static function thumbnailTitleMeta($thumbnail, $title, $meta = null, $url = null): string
  {
    $safeTitle = e($title);
    $safeMeta = e($meta);
    $safeUrl = $url ? e($url) : 'javascript:void(0)';
    $safeThumbnail = e($thumbnail);

    return '
      <div class="d-flex justify-content-start align-items-center user-name">
        <div class="avatar-wrapper">
          <div class="avatar avatar me-2 rounded-2 bg-label-secondary">
            <img src="'. $safeThumbnail .'" alt="Avatar" class="rounded-2">
          </div>
        </div>
        <div class="d-flex flex-column">
          <a href="'. $safeUrl .'" class="text-body text-truncate">
            <span class="fw-medium">'. $safeTitle .'</span>
          </a>' .
      ($meta ? '<small class="text-muted">'. $safeMeta .'</small>' : '') .
      '</div>
      </div>';
  }
}
