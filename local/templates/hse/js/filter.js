document.addEventListener("DOMContentLoaded", function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
  
    filterButtons.forEach(button => {
      button.addEventListener('click', function() {
        // Find currently active button and remove the class
        const currentActive = document.querySelector('.filter-btn.active');
        if (currentActive) {
          gsap.to(currentActive, {
            backgroundColor: 'white',
            color: '#000000',
            borderColor: '#e0e0e0',
            duration: 0.1,
            ease: "power1.out",
            onComplete: () => {
              currentActive.classList.remove('active');
            }
          });
        }
  
        // Add active class to clicked button
        this.classList.add('active');
  
        // Animate the new active button
        gsap.to(this, {
          backgroundColor: '#1C00EF',
          color: 'white',
          borderColor: '#1C00EF',
          duration: 0.1,
          ease: "power1.out"
        });
  
        // Filter functionality would go here in the future
        // const filterType = this.textContent;
        // filterEvents(filterType);
      });
    });
  
    // 3. Make filter container draggable on mobile
    const filterScroll = document.querySelector('.filter-scroll');
  
    if (filterScroll) {
      let isDragging = false;
      let startX, scrollLeft;
  
      const startDragging = function(e) {
        isDragging = true;
        startX = e.type === 'touchstart' ? e.touches[0].pageX : e.pageX;
        scrollLeft = filterScroll.scrollLeft;
  
        // Add dragging class for styling if needed
        filterScroll.classList.add('dragging');
      };
  
      const stopDragging = function() {
        isDragging = false;
        filterScroll.classList.remove('dragging');
      };
  
      const drag = function(e) {
        if (!isDragging) return;
        e.preventDefault();
  
        const x = e.type === 'touchmove' ? e.touches[0].pageX : e.pageX;
        const distance = x - startX;
        filterScroll.scrollLeft = scrollLeft - distance;
      };
  
      // Mouse events
      filterScroll.addEventListener('mousedown', startDragging);
      filterScroll.addEventListener('mouseleave', stopDragging);
      filterScroll.addEventListener('mouseup', stopDragging);
      filterScroll.addEventListener('mousemove', drag);
  
      // Touch events
      filterScroll.addEventListener('touchstart', startDragging);
      filterScroll.addEventListener('touchend', stopDragging);
      filterScroll.addEventListener('touchmove', drag);
  
      // Prevent click events during drag
      filterScroll.addEventListener('click', function(e) {
        if (filterScroll.classList.contains('dragging')) {
          e.preventDefault();
        }
      });
  
      // Smooth scrolling with mousewheel using GSAP
      filterScroll.addEventListener('wheel', function(e) {
        e.preventDefault();
        gsap.to(filterScroll, {
          scrollLeft: filterScroll.scrollLeft + (e.deltaY * 2),
          duration: 0.5,
          ease: "power2.out"
        });
      });
    }
  });
  