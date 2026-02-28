# Brand Voice Engine: Prompt Engineering Strategy

## 1. The Linguistic Archeologist (Extraction)
**Strategy:** Role-based Reverse Engineering.
**Prompt:** `You are a linguistics expert. Analyze these 3-5 text samples to create a structured Brand Voice Profile. Return ONLY a JSON object...`
**Intent:** By forcing a JSON structure (`tone`, `formality`, `patterns`, `persona`), we ensure the data is immediately usable by our Laravel Postgres `jsonb` column and the Quality Gate logic.

## 2. The Mimic (Generation)
**Strategy:** Constraint Anchoring.
**Prompt:** `Act as: {persona}. Tone: {tone}. Formality: {formality}/10. Patterns: {patterns}.`
**Intent:** Style constraints are placed in the **System Prompt** to anchor the LLM's "worldview" before it processes the task, preventing "generic AI drift."

## 3. The Editor (Quality Gate)
**Strategy:** Adversarial Evaluation.
**Prompt:** `Compare this text to this profile: {profile}. Score the match from 1-100.`
**Intent:** Acts as a hurdle. If the match score is < 80, the service triggers a recursive retry (max 2), ensuring users only receive high-fidelity content.
