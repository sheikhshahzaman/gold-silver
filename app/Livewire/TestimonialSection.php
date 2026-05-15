<?php

namespace App\Livewire;

use App\Models\Testimonial;
use Livewire\Attributes\Validate;
use Livewire\Component;

class TestimonialSection extends Component
{
    public bool $formOpen = false;
    public bool $submitted = false;

    #[Validate('required|string|min:2|max:80')]
    public string $name = '';

    #[Validate('nullable|string|max:80')]
    public string $location = '';

    #[Validate('required|integer|min:1|max:5')]
    public int $stars = 5;

    #[Validate('required|string|min:10|max:600')]
    public string $text = '';

    public function openForm(): void
    {
        $this->formOpen = true;
        $this->submitted = false;
    }

    public function closeForm(): void
    {
        $this->formOpen = false;
        $this->reset(['name', 'location', 'stars', 'text']);
        $this->stars = 5;
        $this->resetErrorBag();
    }

    public function submit(): void
    {
        $this->validate();

        $words = preg_split('/\s+/', trim($this->name));
        $initials = strtoupper(
            (substr($words[0] ?? '', 0, 1)) . (substr($words[1] ?? '', 0, 1))
        );

        Testimonial::create([
            'name' => trim($this->name),
            'location' => trim($this->location) ?: null,
            'initials' => $initials ?: 'A',
            'text' => trim($this->text),
            'stars' => $this->stars,
            'is_active' => true,
            'status' => Testimonial::STATUS_PENDING,
            'sort_order' => 0,
        ]);

        $this->submitted = true;
        $this->reset(['name', 'location', 'text']);
        $this->stars = 5;
    }

    public function render()
    {
        $approved = Testimonial::active()->ordered()->get();

        // Split into two columns for the opposite-direction marquees.
        // Even indices → column 1 (scrolls up), odd indices → column 2 (scrolls down).
        $columnA = $approved->values()->filter(fn ($_, $i) => $i % 2 === 0)->values();
        $columnB = $approved->values()->filter(fn ($_, $i) => $i % 2 === 1)->values();

        return view('livewire.testimonial-section', [
            'columnA' => $columnA,
            'columnB' => $columnB,
            'total' => $approved->count(),
        ]);
    }
}
