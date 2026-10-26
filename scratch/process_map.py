import numpy as np
from PIL import Image
from scipy.ndimage import distance_transform_edt, label
import json

img = Image.open(r'C:\Users\USUARIO\.gemini\antigravity\brain\66641874-be02-494a-9138-9652fa86caab\.user_uploaded\media_1792960931666.png').convert('RGB')
arr = np.array(img, dtype=float)

seeds = {
    'vacadiez': (170, 70),
    'ballivian': (80, 270),
    'yacuma': (160, 210),
    'mamore': (235, 200),
    'itenez': (330, 240),
    'cercado': (270, 330),
    'moxos': (180, 460),
    'marban': (260, 430)
}
names = list(seeds.keys())
c_mat = np.array([arr[seeds[k][1], seeds[k][0]] for k in names])

# Pure white background
is_white = (arr[:, :, 0] > 235) & (arr[:, :, 1] > 235) & (arr[:, :, 2] > 235) & (np.std(arr, axis=2) < 15)
bg_labeled, _ = label(is_white)
outside_bg = (bg_labeled == bg_labeled[0, 0])
main_mask = ~outside_bg
main_labeled, num_comp = label(main_mask)
sizes = [np.sum(main_labeled == c) for c in range(1, num_comp + 1)]
dept_mask = (main_labeled == (np.argmax(sizes) + 1))

dists = np.linalg.norm(arr[:, :, None, :] - c_mat[None, None, :, :], axis=3)
closest = np.argmin(dists, axis=2)
min_dist = np.min(dists, axis=2)

is_black_border = (arr[:, :, 0] < 80) & (arr[:, :, 1] < 80) & (arr[:, :, 2] < 80)

# Valid colored pixel: within dept, not black border, matches nearest seed within 40
valid_colored = dept_mask & (min_dist < 40) & (~is_black_border)

# Keep the connected component connected to the seed point (or seed radius of 5)
final_seed_mask = np.zeros(arr.shape[:2], dtype=int) - 1
for i, name in enumerate(names):
    prov_colored = valid_colored & (closest == i)
    prov_labeled, num_p = label(prov_colored)
    sx, sy = seeds[name]
    # Check neighborhood around seed
    sub = prov_labeled[max(0, sy-5):min(506, sy+6), max(0, sx-5):min(500, sx+6)]
    unique_vals = np.unique(sub)
    unique_vals = unique_vals[unique_vals > 0]
    if len(unique_vals) > 0:
        # Match largest in neighborhood
        comp_id = unique_vals[0]
        final_seed_mask[prov_labeled == comp_id] = i
    else:
        # Fallback to largest
        p_sizes = [np.sum(prov_labeled == c) for c in range(1, num_p+1)]
        if len(p_sizes) > 0:
            final_seed_mask[prov_labeled == (np.argmax(p_sizes)+1)] = i

edt, inds = distance_transform_edt(final_seed_mask == -1, return_indices=True)
dept_prov_map = final_seed_mask[inds[0], inds[1]]
dept_prov_map[~dept_mask] = -1

for i, name in enumerate(names):
    ys, xs = np.where(dept_prov_map == i)
    print(f'{name} (id {i}): count={len(xs)}, x=[{xs.min()}, {xs.max()}], y=[{ys.min()}, {ys.max()}]')

def extract_province_polygon(label_map, p_id):
    H, W = label_map.shape
    mask = (label_map == p_id)
    edges = {}
    for r in range(H):
        for c in range(W):
            if mask[r, c]:
                if r == 0 or not mask[r-1, c]:
                    edges[(c, r)] = (c+1, r)
                if r == H-1 or not mask[r+1, c]:
                    edges[(c+1, r+1)] = (c, r+1)
                if c == 0 or not mask[r, c-1]:
                    edges[(c, r+1)] = (c, r)
                if c == W-1 or not mask[r, c+1]:
                    edges[(c+1, r)] = (c+1, r+1)
    visited = set()
    cycles = []
    for start in edges:
        if start in visited:
            continue
        curr = start
        cycle = []
        while curr in edges and curr not in visited:
            visited.add(curr)
            cycle.append(curr)
            curr = edges[curr]
            if curr == start:
                cycles.append(cycle)
                break
    if not cycles:
        return []
    cycles.sort(key=lambda c: len(c), reverse=True)
    return cycles[0]

def rdp(pts, eps=1.3):
    if len(pts) < 3:
        return pts
    p1 = np.array(pts[0])
    p2 = np.array(pts[-1])
    line_vec = p2 - p1
    line_len = np.linalg.norm(line_vec)
    if line_len == 0:
        dists = np.linalg.norm(np.array(pts) - p1, axis=1)
    else:
        line_unit = line_vec / line_len
        vecs = np.array(pts) - p1
        proj = np.dot(vecs, line_unit)[:, None] * line_unit
        dists = np.linalg.norm(vecs - proj, axis=1)
    max_idx = np.argmax(dists)
    if dists[max_idx] > eps:
        left = rdp(pts[:max_idx+1], eps)
        right = rdp(pts[max_idx:], eps)
        return left[:-1] + right
    else:
        return [pts[0], pts[-1]]

svg_paths = {}
centers_calc = {}

for i, name in enumerate(names):
    poly = extract_province_polygon(dept_prov_map, i)
    poly.append(poly[0])
    simp = rdp(poly, eps=1.3)
    
    # Calculate visual center (median/mean of interior points)
    ys, xs = np.where(dept_prov_map == i)
    centers_calc[name] = (int(np.median(xs)), int(np.median(ys)))
    
    d = 'M ' + ' L '.join(f'{x} {y}' for x, y in simp) + ' Z'
    svg_paths[name] = d
    print(f'{name}: simplified {len(simp)} points. Visual center: {centers_calc[name]}')

with open(r'C:\laragon\www\benitours\scratch\clean_paths.json', 'w') as f:
    json.dump({'paths': svg_paths, 'centers': centers_calc}, f, indent=2)

print('Success!')
