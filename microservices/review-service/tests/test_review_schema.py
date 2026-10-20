import unittest

from app.exceptions import ValidationError
from app.schemas.review_schema import validate_create_review, validate_update_review


class ReviewSchemaTests(unittest.TestCase):
    def test_create_review_accepts_valid_data_and_defaults_comment(self):
        self.assertEqual(
            validate_create_review({"product_id": 1, "user_id": 7, "rating": 5}),
            {"product_id": 1, "user_id": 7, "rating": 5, "comment": ""},
        )

    def test_create_review_rejects_bool_and_invalid_rating(self):
        with self.assertRaises(ValidationError) as raised:
            validate_create_review(
                {"product_id": True, "user_id": 7, "rating": False, "comment": ""}
            )

        self.assertIn("product_id", raised.exception.errors)
        self.assertIn("rating", raised.exception.errors)

    def test_create_review_rejects_long_comment(self):
        with self.assertRaises(ValidationError) as raised:
            validate_create_review(
                {
                    "product_id": 1,
                    "user_id": 7,
                    "rating": 5,
                    "comment": "x" * 1001,
                }
            )

        self.assertIn("comment", raised.exception.errors)

    def test_update_requires_at_least_one_supported_field(self):
        with self.assertRaises(ValidationError) as raised:
            validate_update_review({})

        self.assertIn("body", raised.exception.errors)

    def test_update_accepts_partial_data(self):
        self.assertEqual(validate_update_review({"comment": "Bagus"}), {"comment": "Bagus"})


if __name__ == "__main__":
    unittest.main()
