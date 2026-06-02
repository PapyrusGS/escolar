import { gsap } from 'gsap';

const reduced = () =>
  typeof window !== 'undefined' &&
  window.matchMedia &&
  window.matchMedia('(prefers-reduced-motion: reduce)').matches;

export function useGsap() {
  const fadeIn = (target, opts = {}) => {
    if (reduced()) return;
    return gsap.from(target, {
      opacity: 0,
      y: 12,
      duration: 0.4,
      ease: 'power3.out',
      ...opts,
    });
  };

  const staggerIn = (targets, opts = {}) => {
    if (reduced()) return;
    return gsap.from(targets, {
      opacity: 0,
      y: 16,
      duration: 0.45,
      ease: 'power3.out',
      stagger: 0.05,
      ...opts,
    });
  };

  const scaleIn = (target, opts = {}) => {
    if (reduced()) return;
    return gsap.from(target, {
      opacity: 0,
      scale: 0.96,
      duration: 0.3,
      ease: 'power2.out',
      ...opts,
    });
  };

  const pageEnter = (target) => {
    if (reduced()) return;
    return gsap.from(target, {
      opacity: 0,
      y: 14,
      duration: 0.5,
      ease: 'power3.out',
    });
  };

  const slideInRight = (target, opts = {}) => {
    if (reduced()) return;
    return gsap.from(target, {
      opacity: 0,
      x: 20,
      duration: 0.3,
      ease: 'power3.out',
      ...opts,
    });
  };

  const pulse = (target) => {
    if (reduced()) return;
    return gsap.fromTo(
      target,
      { scale: 1 },
      { scale: 1.08, duration: 0.15, yoyo: true, repeat: 1, ease: 'power2.out' }
    );
  };

  const countUp = (target, end, opts = {}) => {
    if (reduced()) {
      if (target) target.textContent = end;
      return;
    }
    const obj = { v: 0 };
    return gsap.to(obj, {
      v: end,
      duration: 1.2,
      ease: 'power2.out',
      ...opts,
      onUpdate() {
        if (target) target.textContent = Math.round(obj.v);
      },
    });
  };

  return {
    fadeIn,
    staggerIn,
    scaleIn,
    pageEnter,
    slideInRight,
    pulse,
    countUp,
  };
}
