<section class="bg-[#4d47f5] py-20 relative overflow-hidden">
  <div
    class="absolute inset-0 bg-gradient-to-r from-[#4d47f5] to-[#0EA5E9] animate-gradient"
  ></div>

  <div class="container mx-auto px-4 text-center relative z-10">
    <h2
      class="text-3xl font-bold mb-8 text-[#0F172A] opacity-0 transform translate-y-5 animate-fadeIn"
    >
      ¿Listo para vivir el deporte como nunca antes?
    </h2>

    <p
      class="text-xl mb-8 text-[#0F172A] opacity-0 transform translate-y-5 animate-fadeIn delay-200"
    >
      Únete a miles de fanáticos que ya disfrutan de Deporstar
    </p>

    <div class="opacity-0 transform translate-y-5 animate-fadeIn delay-400">
      <a
        href="../login"
        class="bg-[#FFFFFF] text-[#1a2537] hover:bg-[#ffffff] px-8 py-6 text-[#1a2537] rounded-md inline-block transform transition-transform duration-200 ease-in-out hover:scale-105 active:scale-95"
      >
        Iniciar Sesión
      </a>
    </div>
  </div>
</section>

<style>
  @keyframes gradient-animation {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.1); opacity: 0.7; }
  }

  @keyframes fadeIn {
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .animate-gradient {
    animation: gradient-animation 5s infinite alternate;
  }

  .animate-fadeIn {
    animation: fadeIn 0.5s forwards;
  }

  .delay-200 {
    animation-delay: 0.2s;
  }

  .delay-400 {
    animation-delay: 0.4s;
  }
</style>
