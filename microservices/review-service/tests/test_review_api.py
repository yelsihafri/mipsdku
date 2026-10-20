import unittest
from unittest.mock import patch

from app import create_app
from app.exceptions import (
    DuplicateReviewError,
    ProductNotFoundError,
    ProductServiceUnavailableError,
    ReviewNotFoundError,
)


class ReviewApiTests(unittest.TestCase):
    def setUp(self):
        with patch("app.init_db"):
            self.app = create_app()
        self.app.debug = False
        self.client = self.app.test_client()

    def test_health_check(self):
        response = self.client.get("/health")

        self.assertEqual(response.status_code, 200)
        self.assertEqual(response.get_json()["status"], "ok")

    def test_invalid_create_request_returns_validation_errors(self):
        with patch("app.controllers.review_controller.review_service.create_review") as create:
            response = self.client.post("/reviews", json={"product_id": 1})

        self.assertEqual(response.status_code, 422)
        self.assertIn("rating", response.get_json()["errors"])
        create.assert_not_called()

    def test_missing_product_returns_422(self):
        with patch(
            "app.controllers.review_controller.review_service.create_review",
            side_effect=ProductNotFoundError(999),
        ):
            response = self.client.post(
                "/reviews",
                json={"product_id": 999, "user_id": 7, "rating": 5},
            )

        self.assertEqual(response.status_code, 422)
        self.assertIn("product_id", response.get_json()["errors"])

    def test_unavailable_product_service_returns_503(self):
        with patch(
            "app.controllers.review_controller.review_service.create_review",
            side_effect=ProductServiceUnavailableError("unavailable"),
        ):
            response = self.client.post(
                "/reviews",
                json={"product_id": 1, "user_id": 7, "rating": 5},
            )

        self.assertEqual(response.status_code, 503)

    def test_duplicate_review_returns_409(self):
        with patch(
            "app.controllers.review_controller.review_service.create_review",
            side_effect=DuplicateReviewError(7, 1),
        ):
            response = self.client.post(
                "/reviews",
                json={"product_id": 1, "user_id": 7, "rating": 5},
            )

        self.assertEqual(response.status_code, 409)

    def test_missing_review_returns_404(self):
        with patch(
            "app.controllers.review_controller.review_service.get_review",
            side_effect=ReviewNotFoundError("ffffffffffffffffffffffff"),
        ):
            response = self.client.get("/reviews/ffffffffffffffffffffffff")

        self.assertEqual(response.status_code, 404)

    def test_unknown_endpoint_returns_json_404(self):
        response = self.client.get("/not-a-route")

        self.assertEqual(response.status_code, 404)
        self.assertIn("message", response.get_json())


if __name__ == "__main__":
    unittest.main()
