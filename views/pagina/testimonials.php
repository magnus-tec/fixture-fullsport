<?php
  $testimonials = [
    [
      'name' => 'Carlos Rodríguez',
      'role' => 'Fanático del fútbol',
      'content' => 'Deporstar ha revolucionado la forma en que sigo a mi equipo. Las estadísticas en tiempo real y los análisis profundos son increíbles.',
      'image' => 'public/img/usuario1.png'
    ],
    [
      'name' => 'Laura Martínez',
      'role' => 'Entrenadora de baloncesto',
      'content' => 'Como entrenadora, los análisis avanzados de Deporstar me han ayudado a mejorar las estrategias de mi equipo. Una herramienta imprescindible.',
      'image' => 'public/img/usuario2.png'
    ],
    [
      'name' => 'Javier López',
      'role' => 'Periodista deportivo',
      'content' => 'La plataforma de Deporstar es mi fuente principal de información. Rápida, precisa y siempre actualizada. No puedo imaginar mi trabajo sin ella.',
      'image' => 'public/img/usuario1.png'
    ]
  ];
?>
<section class="bg-[#1E293B] py-20">
  <div class="container mx-auto px-4">
    <h2 class="text-3xl font-bold text-center text-white mb-12">Lo que dicen nuestros usuarios</h2>
    <div class="max-w-4xl mx-auto">
      <div id="testimonial-container" class="bg-[#0F172A] p-8 rounded-lg shadow-xl opacity-0 transform translate-x-12 transition-all duration-500">
        <p id="testimonial-content" class="text-gray-300 mb-6 text-lg italic">
          <?php echo $testimonials[0]['content']; ?>
        </p>
        <div class="flex items-center">
          <img id="testimonial-image" src="<?php echo $testimonials[0]['image']; ?>" width="80" height="80" alt="<?php echo $testimonials[0]['name']; ?>" class="rounded-full mr-4" />
          <div>
            <h3 id="testimonial-name" class="font-semibold text-white"><?php echo $testimonials[0]['name']; ?></h3>
            <p id="testimonial-role" class="text-gray-400"><?php echo $testimonials[0]['role']; ?></p>
          </div>
        </div>
      </div>
      <div class="flex justify-center mt-8 space-x-4">
        <button id="prev-button" class="p-2 rounded-full bg-[#4d47f5] text-[#0F172A] hover:bg-[#0EA5E9]">
          <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
          </svg>
        </button>
        <button id="next-button" class="p-2 rounded-full bg-[#4d47f5] text-[#0F172A] hover:bg-[#0EA5E9]">
          <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </button>
      </div>
    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/framer-motion@latest"></script>
<script>
  let currentTestimonial = 0;
  const testimonials = <?php echo json_encode($testimonials); ?>;
  const testimonialContent = document.getElementById('testimonial-content');
  const testimonialImage = document.getElementById('testimonial-image');
  const testimonialName = document.getElementById('testimonial-name');
  const testimonialRole = document.getElementById('testimonial-role');
  const testimonialContainer = document.getElementById('testimonial-container');

  const prevButton = document.getElementById('prev-button');
  const nextButton = document.getElementById('next-button');

  const updateTestimonial = () => {
    const testimonial = testimonials[currentTestimonial];
    testimonialContent.textContent = testimonial.content;
    testimonialImage.src = testimonial.image;
    testimonialName.textContent = testimonial.name;
    testimonialRole.textContent = testimonial.role;

    // Animate testimonial container in with opacity and slide in
    testimonialContainer.classList.remove('opacity-0', 'translate-x-12');
    testimonialContainer.classList.add('opacity-100', 'translate-x-0');
  };

  const nextTestimonial = () => {
    // Fade out current testimonial with slide out animation
    testimonialContainer.classList.remove('opacity-100', 'translate-x-0');
    testimonialContainer.classList.add('opacity-0', 'translate-x-12');

    currentTestimonial = (currentTestimonial + 1) % testimonials.length;
    setTimeout(updateTestimonial, 500); // Delay the update to match the animation time
  };

  const prevTestimonial = () => {
    // Fade out current testimonial with slide out animation
    testimonialContainer.classList.remove('opacity-100', 'translate-x-0');
    testimonialContainer.classList.add('opacity-0', 'translate-x--12');

    currentTestimonial = (currentTestimonial - 1 + testimonials.length) % testimonials.length;
    setTimeout(updateTestimonial, 500); // Delay the update to match the animation time
  };

  // Automatically change testimonial every 5 seconds
  setInterval(nextTestimonial, 5000);

  prevButton.addEventListener('click', prevTestimonial);
  nextButton.addEventListener('click', nextTestimonial);

  // Initialize the first testimonial with animation
  setTimeout(updateTestimonial, 500);
</script>

<style>
  #testimonial-container {
    transition: opacity 0.5s ease-in-out, transform 0.5s ease-in-out;
  }
</style>
